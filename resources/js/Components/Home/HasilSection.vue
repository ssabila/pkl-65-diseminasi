<template>
  <section class="hasil-section">
    <div class="content-wrapper">
      <!-- Header Section -->
      <div class="header-container">
        <img src="/images/assets/gunungan3.png" alt="Wayang Icon" class="wayang-icon" />
        <img src="/images/assets/hasil-penelitian.png" alt="Hasil Penelitian" class="header-image" />
      </div>

      <!-- Description -->
      <p class="description">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Berikut adalah rangkuman hasil penelitian dari tiga riset utama.
      </p>

      <!-- Three Research Cards -->
      <div class="research-cards">
        <!-- Card 01 -->
        <div class="card">
          <div class="card-number">01</div>
          <h3 class="card-title">Riset 1: Temuan</h3>
          <p class="card-text">
            Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt.
          </p>
          <button class="detail-button">Lihat Detail</button>
        </div>

        <!-- Card 02 -->
        <div class="card">
          <div class="card-number">02</div>
          <h3 class="card-title">Riset 2: Temuan</h3>
          <p class="card-text">
            Ut enim ad minim veniam quis nostrud exercitation ullamco laboris nisi aliquip.
          </p>
          <button class="detail-button">Lihat Detail</button>
        </div>

        <!-- Card 03 -->
        <div class="card">
          <div class="card-number">03</div>
          <h3 class="card-title">Riset 5: Temuan</h3>
          <p class="card-text">
            Duis aute irure dolor in reprehenderit voluptate velit esse cillum dolore.
          </p>
          <button class="detail-button">Lihat Detail →</button>
        </div>
      </div>

      <!-- Kesimpulan Section -->
      <div class="kesimpulan-container">
        <h2 class="kesimpulan-title">Kesimpulan Umum</h2>

        <div class="kesimpulan-content">
          <!-- Left Column -->
          <div class="column">
            <div class="point-item">
              <span class="check-icon">✓</span>
              <div class="point-text">
                <h4>Poin Utama</h4>
              </div>
            </div>

            <div class="point-item">
              <span class="check-icon">✓</span>
              <p class="point-text">Lorem ipsum dolor sit amet consectetur adipiscing elit</p>
            </div>

            <div class="point-item">
              <span class="check-icon">✓</span>
              <p class="point-text">Sed do eiusmod tempor incididunt ut labore dolore</p>
            </div>

            <div class="point-item">
              <span class="check-icon">✓</span>
              <p class="point-text">Quis nostrud exercitation ullamco laboris nisi</p>
            </div>
          </div>

          <!-- Right Column -->
          <div class="column">
            <div class="point-item">
              <span class="star-icon">✦</span>
              <div class="point-text">
                <h4>Rekomendasi</h4>
              </div>
            </div>

            <div class="point-item">
              <span class="check-icon">✓</span>
              <p class="point-text">Excepteur sint occaecat cupidatat non proident</p>
            </div>

            <div class="point-item">
              <span class="check-icon">✓</span>
              <p class="point-text">Culpa qui officia deserunt mollit anim id est</p>
            </div>

            <div class="point-item">
              <span class="check-icon">✓</span>
              <p class="point-text">Sed ut perspiciatis unde omnis iste natus</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Background Ornamen Candi -->
    <div class="ornamen-candi">
      <img src="/images/assets/landmark-candi-tiga.svg" alt="Ornamen Candi" class="candi-image" />
    </div>
  </section>
</template>

<script>
export default {
  name: 'HasilSection'
}
</script>

<style scoped>
.hasil-section {
  background: #EF874B;
  padding: 80px 120px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: flex-start;
  gap: 40px;
}

.content-wrapper {
  flex: 1;
  position: relative;
  z-index: 2;
}

/* Ornamen Candi Background */
.ornamen-candi {
  position: relative;
  width: 450px;
  height: auto;
  flex-shrink: 0;
  z-index: 1;
  align-self: flex-end;
  margin-bottom: -80px;
  margin-right: -300px;
}

.candi-image {
  width: 100%;
  height: auto;
  opacity: 0.9;
}

/* Header */
.header-container {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 20px;
  position: relative;
  z-index: 2;
}

.header-image {
  height: 60px;
  width: auto;
}

.wayang-icon {
  width: 60px;
  height: 60px;
}

/* Description */
.description {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  line-height: 1.8;
  color: white;
  margin-bottom: 40px;
  max-width: 600px;
  position: relative;
  z-index: 2;
}

/* Research Cards */
.research-cards {
  display: flex;
  gap: 30px;
  margin-bottom: 60px;
  position: relative;
  z-index: 2;
}

.card {
  background-color: transparent;
  border-left: 4px solid #FFFBDF;
  padding: 30px 25px;
  flex: 1;
  border-radius: 0;
}

.card-number {
  font-family: 'Playfair Display', serif;
  font-size: 48px;
  font-weight: 700;
  color: white;
  margin-bottom: 15px;
}

.card-title {
  font-family: 'Playfair Display', serif;
  font-size: 22px;
  font-weight: 600;
  color: white;
  margin: 0 0 15px 0;
}

.card-text {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  line-height: 1.6;
  color: white;
  margin-bottom: 20px;
}

.detail-button {
  background-color: white;
  color: #D97757;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.detail-button:hover {
  background-color: #f0f0f0;
  transform: translateY(-2px);
}

/* Kesimpulan Section */
.kesimpulan-container {
  background-color: rgba(255, 200, 150, 0.25);
  backdrop-filter: blur(5px);
  padding: 40px 50px;
  border-radius: 20px;
  position: relative;
  z-index: 2;
}

.kesimpulan-title {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  font-weight: 700;
  color: white;
  text-align: center;
  margin: 0 0 40px 0;
}

.kesimpulan-content {
  display: flex;
  gap: 60px;
}

.column {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.point-item {
  display: flex;
  align-items: flex-start;
  gap: 15px;
}

.check-icon,
.star-icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  margin-top: 2px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: white;
  font-weight: bold;
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  padding: 4px;
}

.point-text {
  flex: 1;
}

.point-text h4 {
  font-family: 'Poppins', sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: white;
  margin: 0 0 5px 0;
}

.point-text p {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  line-height: 1.6;
  color: white;
  margin: 0;
}

.point-item > p.point-text {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  line-height: 1.6;
  color: white;
  margin: 0;
}

/* Responsive */
@media (max-width: 1024px) {
  .hasil-section {
    padding: 60px 80px;
    flex-direction: column;
  }

  .ornamen-candi {
    width: 350px;
    margin-bottom: -60px;
    margin-right: -80px;
    align-self: flex-end;
  }

  .research-cards {
    flex-direction: column;
  }

  .kesimpulan-content {
    flex-direction: column;
    gap: 30px;
  }
}

@media (max-width: 768px) {
  .hasil-section {
    padding: 40px 30px;
  }

  .header-image {
    height: 45px;
  }

  .wayang-icon {
    width: 45px;
    height: 45px;
  }

  .card-number {
    font-size: 36px;
  }

  .card-title {
    font-size: 18px;
  }

  .kesimpulan-title {
    font-size: 28px;
  }

  .ornamen-candi {
    width: 250px;
    margin-bottom: -40px;
    margin-right: -30px;
  }
}
</style>