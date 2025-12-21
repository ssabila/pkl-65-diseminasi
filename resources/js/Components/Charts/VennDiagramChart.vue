<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b', '#748d63'] }
});

const canvasRef = ref(null);

// Parse Venn Diagram data
// Expected format: { vennData: { sets: [{name: 'A', size: 100}], overlaps: [{sets: ['A', 'B'], size: 30}] } }
const vennData = computed(() => props.chartData?.vennData || { sets: [], overlaps: [] });

// Calculate exclusive values for each region
const calculateExclusiveValues = () => {
    const sets = vennData.value.sets || [];
    const overlaps = vennData.value.overlaps || [];
    
    if (sets.length === 0) return { exclusives: [], pairwiseExclusives: [], centerValue: 0 };
    
    // Get overlap values
    const getOverlapSize = (setNames) => {
        const overlap = overlaps.find(o => {
            if (o.sets.length !== setNames.length) return false;
            return setNames.every(name => o.sets.includes(name));
        });
        return overlap?.size || 0;
    };
    
    if (sets.length === 2) {
        const abOverlap = getOverlapSize([sets[0].name, sets[1].name]);
        
        // Exclusive A = Total A - (A∩B)
        const exclusiveA = sets[0].size - abOverlap;
        // Exclusive B = Total B - (A∩B)
        const exclusiveB = sets[1].size - abOverlap;
        
        return {
            exclusives: [exclusiveA, exclusiveB],
            pairwiseExclusives: [],
            centerValue: abOverlap
        };
    }
    
    if (sets.length === 3) {
        const abOverlap = getOverlapSize([sets[0].name, sets[1].name]);
        const acOverlap = getOverlapSize([sets[0].name, sets[2].name]);
        const bcOverlap = getOverlapSize([sets[1].name, sets[2].name]);
        const abcOverlap = getOverlapSize([sets[0].name, sets[1].name, sets[2].name]);
        
        // For 3-set Venn diagram, pairwise overlaps entered by user represent EXCLUSIVE pairwise regions
        // The value shown in A∩B region (excluding C) is what user enters as A∩B overlap
        // The center value (A∩B∩C) is what user enters as the 3-way overlap
        
        // Exclusive A = Total A - (A∩B exclusive) - (A∩C exclusive) - (A∩B∩C)
        // But the user enters A∩B as the pairwise exclusive (not including center)
        // So we interpret: user's A∩B input = the displayed value in A∩B-only region
        
        // Actually, let's use the standard interpretation:
        // User enters: Total size of A, Total size of B, Total size of C
        // User enters: Size of A∩B (including A∩B∩C), Size of A∩C (including A∩B∩C), Size of B∩C (including A∩B∩C)
        // User enters: Size of A∩B∩C
        
        // Then exclusive values are:
        // A only = A - (A∩B) - (A∩C) + (A∩B∩C)  [inclusion-exclusion]
        // A∩B only (not C) = (A∩B) - (A∩B∩C)
        
        const exclusiveA = sets[0].size - abOverlap - acOverlap + abcOverlap;
        const exclusiveB = sets[1].size - abOverlap - bcOverlap + abcOverlap;
        const exclusiveC = sets[2].size - acOverlap - bcOverlap + abcOverlap;
        
        const exclusiveAB = abOverlap - abcOverlap; // A∩B only (not in C)
        const exclusiveAC = acOverlap - abcOverlap; // A∩C only (not in B)
        const exclusiveBC = bcOverlap - abcOverlap; // B∩C only (not in A)
        
        return {
            exclusives: [exclusiveA, exclusiveB, exclusiveC],
            pairwiseExclusives: [exclusiveAB, exclusiveAC, exclusiveBC],
            centerValue: abcOverlap
        };
    }
    
    return { exclusives: [sets[0].size], pairwiseExclusives: [], centerValue: 0 };
};

const drawVennDiagram = () => {
    if (!canvasRef.value) return;

    const canvas = canvasRef.value;
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;

    // Clear canvas
    ctx.clearRect(0, 0, width, height);

    const sets = vennData.value.sets || [];

    if (sets.length === 0) {
        ctx.fillStyle = '#666';
        ctx.font = '14px TT Bells, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('No data available', width / 2, height / 2);
        return;
    }

    // Calculate exclusive values
    const { exclusives, pairwiseExclusives, centerValue } = calculateExclusiveValues();

    // Calculate circle properties
    const centerX = width / 2;
    const centerY = height / 2;
    const baseRadius = Math.min(width, height) * 0.25;

    // Draw circles based on number of sets
    if (sets.length === 2) {
        // Two circles - proper Venn diagram positioning
        const offset = baseRadius * 0.7;
        const leftX = centerX - offset;
        const rightX = centerX + offset;
        
        // Draw circles
        drawCircle(ctx, leftX, centerY, baseRadius, props.colors[0], 0.35);
        drawCircle(ctx, rightX, centerY, baseRadius, props.colors[1], 0.35);
        
        // Calculate label positions
        const leftOnlyX = leftX - baseRadius * 0.45;
        const rightOnlyX = rightX + baseRadius * 0.45;
        
        // Draw labels with set name and exclusive value
        // Left set - exclusive part
        drawLabelWithBackground(ctx, sets[0].name, exclusives[0], leftOnlyX, centerY, props.colors[0]);
        
        // Right set - exclusive part  
        drawLabelWithBackground(ctx, sets[1].name, exclusives[1], rightOnlyX, centerY, props.colors[1]);
        
        // Center overlap
        if (centerValue > 0) {
            drawValueLabel(ctx, centerValue, centerX, centerY);
        }
        
        // Draw totals at the bottom
        drawTotalLabels(ctx, sets, width, height);
        
    } else if (sets.length === 3) {
        // Three circles - proper Venn diagram with all overlaps
        const offset = baseRadius * 0.65;
        const topY = centerY - offset * 0.6;
        const bottomY = centerY + offset * 0.6;
        const leftX = centerX - offset * 0.85;
        const rightX = centerX + offset * 0.85;
        
        // Draw circles
        drawCircle(ctx, centerX, topY, baseRadius, props.colors[0], 0.35);
        drawCircle(ctx, leftX, bottomY, baseRadius, props.colors[1], 0.35);
        drawCircle(ctx, rightX, bottomY, baseRadius, props.colors[2], 0.35);
        
        // Draw set names and exclusive values in exclusive regions
        // Top circle (A) - exclusive area at top
        drawLabelWithBackground(ctx, sets[0].name, exclusives[0], centerX, topY - baseRadius * 0.4, props.colors[0]);
        
        // Bottom-left circle (B) - exclusive area at bottom-left
        drawLabelWithBackground(ctx, sets[1].name, exclusives[1], leftX - baseRadius * 0.35, bottomY + baseRadius * 0.35, props.colors[1]);
        
        // Bottom-right circle (C) - exclusive area at bottom-right
        drawLabelWithBackground(ctx, sets[2].name, exclusives[2], rightX + baseRadius * 0.35, bottomY + baseRadius * 0.35, props.colors[2]);
        
        // Pairwise overlaps (excluding center)
        // A∩B only (between top and bottom-left)
        if (pairwiseExclusives[0] > 0) {
            drawValueLabel(ctx, pairwiseExclusives[0], centerX - offset * 0.45, centerY - offset * 0.15);
        }
        
        // A∩C only (between top and bottom-right)
        if (pairwiseExclusives[1] > 0) {
            drawValueLabel(ctx, pairwiseExclusives[1], centerX + offset * 0.45, centerY - offset * 0.15);
        }
        
        // B∩C only (between bottom-left and bottom-right)
        if (pairwiseExclusives[2] > 0) {
            drawValueLabel(ctx, pairwiseExclusives[2], centerX, centerY + offset * 0.55);
        }
        
        // Center overlap (A∩B∩C)
        if (centerValue > 0) {
            drawValueLabel(ctx, centerValue, centerX, centerY);
        }
        
        // Draw totals at the bottom
        drawTotalLabels(ctx, sets, width, height);
        
    } else {
        // Single circle
        drawCircle(ctx, centerX, centerY, baseRadius, props.colors[0], 0.35);
        drawLabelWithBackground(ctx, sets[0].name, sets[0].size, centerX, centerY, props.colors[0]);
    }
};

const drawCircle = (ctx, x, y, radius, color, alpha) => {
    ctx.globalAlpha = alpha;
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, 2 * Math.PI);
    ctx.fill();
    
    ctx.globalAlpha = 1;
    ctx.strokeStyle = color;
    ctx.lineWidth = 2.5;
    ctx.stroke();
};

const drawLabelWithBackground = (ctx, name, value, x, y, color) => {
    ctx.globalAlpha = 1;
    
    // Draw name
    ctx.fillStyle = '#1e293b';
    ctx.font = 'bold 14px TT Bells, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(name, x, y - 10);
    
    // Draw value with slight background
    if (value !== null && value !== undefined) {
        const valueStr = value.toString();
        const textWidth = ctx.measureText(valueStr).width;
        
        // Background pill
        ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
        ctx.beginPath();
        ctx.roundRect(x - textWidth/2 - 6, y + 4, textWidth + 12, 22, 4);
        ctx.fill();
        
        // Value text
        ctx.fillStyle = color;
        ctx.font = 'bold 16px TT Bells, sans-serif';
        ctx.fillText(valueStr, x, y + 15);
    }
};

const drawValueLabel = (ctx, value, x, y) => {
    ctx.globalAlpha = 1;
    
    const valueStr = value.toString();
    const textWidth = ctx.measureText(valueStr).width;
    
    // Background pill
    ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
    ctx.beginPath();
    ctx.roundRect(x - textWidth/2 - 8, y - 12, textWidth + 16, 24, 6);
    ctx.fill();
    
    // Border
    ctx.strokeStyle = '#94a3b8';
    ctx.lineWidth = 1;
    ctx.stroke();
    
    // Value text
    ctx.fillStyle = '#1e293b';
    ctx.font = 'bold 14px TT Bells, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(valueStr, x, y);
};

const drawTotalLabels = (ctx, sets, width, height) => {
    ctx.globalAlpha = 1;
    ctx.fillStyle = '#64748b';
    ctx.font = '11px TT Bells, sans-serif';
    ctx.textAlign = 'center';
    
    const totalText = sets.map(s => `${s.name}: ${s.size}`).join('  |  ');
    ctx.fillText(`Totals: ${totalText}`, width / 2, height - 15);
};

const downloadChart = () => {
    if (canvasRef.value) {
        const link = document.createElement('a');
        link.href = canvasRef.value.toDataURL('image/png');
        link.download = `VennDiagram-${props.title || 'Export'}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

onMounted(() => {
    nextTick(() => {
        drawVennDiagram();
    });
});

watch(() => props.chartData, () => {
    nextTick(() => {
        drawVennDiagram();
    });
}, { deep: true });

defineExpose({ downloadChart });
</script>

<template>
    <div class="w-full flex flex-col items-center" :style="{ minHeight: height + 'px' }">
        <canvas 
            ref="canvasRef" 
            :width="600" 
            :height="height"
            class="max-w-full"
        ></canvas>
        
        <!-- Legend explanation -->
        <div class="mt-2 text-xs text-gray-500 text-center">
            <p>Nilai di dalam setiap area menunjukkan jumlah eksklusif (hanya di area tersebut)</p>
        </div>
    </div>
</template>
