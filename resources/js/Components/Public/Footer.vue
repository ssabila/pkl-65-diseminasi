<script setup>
// Data untuk Navigasi
const navLinks = [
    { label: 'Beranda', href: '/' },
    { label: 'Hasil Riset', href: '/hasil-riset' }, 
    { label: 'Login', href: '/login' }
];

// Data Kontak
const contacts = [
    { 
        icon: 'map', 
        text: 'Politeknik Statistika STIS, Jakarta Timur',
        link: 'https://goo.gl/maps/...' 
    },
    { 
        icon: 'mail', 
        text: 'pkl65@stis.ac.id',
        link: 'mailto:pkl65@stis.ac.id'
    },
    { 
        icon: 'globe', 
        text: 'www.stis.ac.id',
        link: 'https://www.stis.ac.id'
    }
];

// Data Media Sosial
const socialMedia = [
    { label: 'Instagram', icon: 'instagram', url: '#' },
    { label: 'YouTube', icon: 'youtube', url: '#' },
    { label: 'Twitter', icon: 'twitter', url: '#' },
    { label: 'Facebook', icon: 'facebook', url: '#' },
];

const assets = {
    logo: '/images/assets/LOGO-PKL_REV8.png'
};
</script>

<template>
    <footer class="bg-gray-900 text-gray-300 py-16 px-6 font-sans relative overflow-hidden border-t-4 border-[#EF874B]">
        
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent opacity-50"
             data-aos="zoom-in"
             data-aos-duration="1500">
        </div>

        <div class="container mx-auto max-w-7xl relative z-10">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8 mb-16">
                
                <div class="lg:col-span-5 space-y-6"
                     data-aos="fade-right"
                     data-aos-duration="1000"
                     data-aos-delay="0">
                    <div class="flex items-center gap-4">
                        <div class="p-2 bg-white rounded-full shadow-lg shadow-orange-500/20">
                            <img :src="assets.logo" alt="Logo PKL 65" class="w-12 h-12 md:w-14 md:h-14 object-contain" />
                        </div>
                        <div>
                            <h3 class="text-2xl md:text-3xl font-headline font-bold text-white leading-none">
                                PKL 65
                            </h3>
                            <p class="text-sm text-orange-400 font-medium">Politeknik Statistika STIS</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-400 text-sm md:text-base leading-relaxed max-w-md">
                        Praktik Kerja Lapangan Mahasiswa Tingkat III Politeknik Statistika STIS Tahun Akademik 2025/2026 di Provinsi D.I. Yogyakarta.
                    </p>
                </div>

                <div class="lg:col-span-3 lg:pl-8"
                     data-aos="fade-up"
                     data-aos-duration="1000"
                     data-aos-delay="200">
                    <h4 class="text-lg font-headline font-bold text-white mb-6 border-b border-gray-700 pb-2 inline-block">
                        Navigasi
                    </h4>
                    <ul class="space-y-3">
                        <li v-for="(nav, index) in navLinks" :key="index">
                            <a :href="nav.href" 
                               class="group flex items-center gap-2 hover:text-orange-400 transition-all duration-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-600 group-hover:bg-orange-500 transition-colors"></span>
                                <span class="group-hover:translate-x-1 transition-transform">{{ nav.label }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="lg:col-span-4"
                     data-aos="fade-left"
                     data-aos-duration="1000"
                     data-aos-delay="400">
                    <h4 class="text-lg font-headline font-bold text-white mb-6 border-b border-gray-700 pb-2 inline-block">
                        Kontak Kami
                    </h4>
                    <ul class="space-y-4">
                        <li v-for="(contact, index) in contacts" :key="index" class="flex items-start gap-4 group">
                            <div class="mt-1 p-2 rounded-lg bg-gray-800 text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300 shadow-sm">
                                <svg v-if="contact.icon === 'map'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg v-if="contact.icon === 'mail'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <svg v-if="contact.icon === 'globe'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <div>
                                <a :href="contact.link" 
                                   :target="contact.icon === 'globe' ? '_blank' : '_self'"
                                   class="text-sm md:text-base hover:text-orange-400 transition-colors break-all block mt-1">
                                    {{ contact.text }}
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-6"
                 data-aos="fade-up"
                 data-aos-duration="1000"
                 data-aos-offset="0"
                 data-aos-delay="600">
                
                <p class="text-sm text-gray-500 text-center md:text-left">
                    &copy; 2025 Politeknik Statistika STIS. All rights reserved.
                </p>

                <div class="flex gap-4">
                    <a v-for="(social, index) in socialMedia" :key="index" :href="social.url" 
                       class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-orange-500 hover:text-white hover:-translate-y-1 transition-all duration-300 shadow-md hover:shadow-orange-500/30"
                       :aria-label="social.label">
                        
                        <svg v-if="social.icon === 'instagram'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        <svg v-if="social.icon === 'facebook'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        <svg v-if="social.icon === 'twitter'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        <svg v-if="social.icon === 'youtube'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.font-headline {
    font-family: 'Georgia', 'Times New Roman', Times, serif;
}
.font-sans {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
</style>