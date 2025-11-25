<template>
  <section class="metode-section">
    <!-- Header Section -->
    <div class="header-container">
      <img src="/images/assets/22.png" alt="Wayang Icon" class="wayang-icon" />
      <img src="/images/assets/metodologi-data.svg" alt="Metode Pengumpulan Data" class="section-title-image" />
    </div>


    <!-- Wawancara Section -->
    <div class="subsection">
      <div class="subsection-header-with-line">
        <img src="/images/assets/bunga1.png" alt="Icon" class="bullet-icon" />
        <div class="vertical-line-connector"></div>
      </div>
      <div class="subsection-content">
        <h3 class="subsection-title">Wawancara</h3>
        <p class="subsection-text">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor. Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.
        </p>
      </div>
    </div>

    <!-- Alur Pengumpulan Data Section -->
    <div class="subsection">
      <div class="subsection-header-with-line">
        <img src="/images/assets/bunga1.png" alt="Icon" class="bullet-icon" />
      </div>
      <div class="subsection-content">
        <h3 class="subsection-title">Alur Pengumpulan Data</h3>
      </div>

      <!-- 5 Steps -->
      <div class="steps-container">
        <div class="step-wrapper">
          <div class="step-item">
            <div class="step-circle">1</div>
            <p class="step-label">Persiapan</p>
          </div>
          <div class="step-arrow">→</div>
        </div>

        <div class="step-wrapper">
          <div class="step-item">
            <div class="step-circle">2</div>
            <p class="step-label">Pelaksanaan</p>
          </div>
          <div class="step-arrow">→</div>
        </div>

        <div class="step-wrapper">
          <div class="step-item">
            <div class="step-circle">3</div>
            <p class="step-label">Pengolahan</p>
          </div>
          <div class="step-arrow">→</div>
        </div>

        <div class="step-wrapper">
          <div class="step-item">
            <div class="step-circle">4</div>
            <p class="step-label">Analisis</p>
          </div>
          <div class="step-arrow">→</div>
        </div>

        <div class="step-wrapper">
          <div class="step-item">
            <div class="step-circle">5</div>
            <p class="step-label">Diseminasi</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Ornamen Batik Bawah -->
    <div class="ornamen-bawah">
      <img 
        src="/images/Group 11 (1).png" 
        alt="Ornamen Batik" 
        class="batik-pattern"
        @error="handleImageError"
      />
    </div>
  </section>
</template>

<script>
export default {
  name: 'MetodeDataSection',
  methods: {
    handleImageError(event) {
      console.error('Image failed to load:', event.target.src);
      // Try alternative paths
      const alternatives = [
        '/images/Group%2011%20(1).png',
        './images/Group 11 (1).png',
        '@/assets/images/Group 11 (1).png',
        '/Group 11 (1).png'
      ];
      
      const currentSrc = event.target.src;
      const nextIndex = alternatives.findIndex(alt => currentSrc.includes(alt)) + 1;
      
      if (nextIndex < alternatives.length) {
        event.target.src = alternatives[nextIndex];
      }
    }
  }
}
</script>

<style scoped>
.metode-section {
  background-color: #FFF5E6;
  padding: 80px 120px 100px 120px;
  position: relative;
  overflow: hidden;
  padding-bottom: 120px;
}

/* Header */
.header-container {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 60px;
}

.wayang-icon {
  width: 60px;
  height: 60px;
}

.section-title {
  font-family: 'Playfair Display', serif;
  font-size: 48px;
  font-weight: 700;
  color: #D97757;
  margin: 0;
}

/* Subsection */
.subsection {
  margin-bottom: 50px;
  position: relative;
}

.subsection-header-with-line {
  position: absolute;
  left: 0;
  top: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.vertical-line-connector {
  width: 2px;
  height: 145px;
  background-color: #333;
  margin-top: 0;
}

.subsection-content {
  margin-left: 50px;
}

.bullet-icon {
  width: 25px;
  height: 25px;
  flex-shrink: 0;
}

.subsection-title {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  font-weight: 600;
  color: #D97757;
  margin: 0 0 15px 0;
}

.subsection-text {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  line-height: 1.8;
  color: #333;
  margin: 0;
}

/* Steps Container */
.steps-container {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-top: 40px;
  padding: 0 20px;
  margin-bottom: 80px;
}

.step-wrapper {
  display: flex;
  align-items: center;
  gap: 30px;
}

.step-wrapper:last-child {
  gap: 0;
}

.step-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.step-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #6B8E6F;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Poppins', sans-serif;
  font-size: 32px;
  font-weight: 600;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.step-arrow {
  font-size: 28px;
  color: #333;
  font-weight: 400;
  margin-top: -50px;
}

.step-label {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  font-weight: 500;
  color: #D97757;
  margin: 0;
  text-align: center;
}

/* Ornamen Bawah */
.ornamen-bawah {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 50px;
  z-index: 10;
}

.batik-pattern {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Responsive */
@media (max-width: 1024px) {
  .metode-section {
    padding: 60px 80px 80px 80px;
  }

  .steps-container {
    flex-wrap: wrap;
    gap: 30px;
  }

  .step-arrow {
    display: none;
  }
}

@media (max-width: 768px) {
  .metode-section {
    padding: 40px 30px 60px 30px;
  }

  .section-title {
    font-size: 32px;
  }

  .subsection-title {
    font-size: 28px;
  }

  .wayang-icon {
    width: 45px;
    height: 45px;
  }

  .step-circle {
    width: 60px;
    height: 60px;
    font-size: 24px;
  }

  .vertical-line-connector {
    height: 80px;
  }

  .subsection-content {
    margin-left: 40px;
  }
}
</style>