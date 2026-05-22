<template>
  <div class="login-container">
    <!-- Background particles -->
    <div class="particles">
      <div class="particle" v-for="i in 20" :key="i"></div>
    </div>

    <!-- Login card -->
    <div class="login-card">
      <!-- Navy SEAL Logo -->
      <div class="logo-section">
        <svg class="navy-logo" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <!-- Circle outer -->
          <circle cx="100" cy="100" r="95" fill="none" stroke="#c9a961" stroke-width="3"/>
          <circle cx="100" cy="100" r="90" fill="none" stroke="#c9a961" stroke-width="1" opacity="0.5"/>
          
          <!-- Text on circle -->
          <defs>
            <path id="circlePath" cx="100" cy="100" r="75" fill="none"/>
            <path d="M 25,100 A 75,75 0 0,1 175,100" id="topPath" fill="none"/>
          </defs>
          <text font-size="12" font-weight="bold" fill="#c9a961" letter-spacing="2">
            <textPath href="#topPath" startOffset="50%" text-anchor="middle">
              NAVY SEAL ARMORY
            </textPath>
          </text>

          <!-- Inner circle -->
          <circle cx="100" cy="100" r="60" fill="none" stroke="#c9a961" stroke-width="2"/>
          
          <!-- Eagle/Shield emblem -->
          <g transform="translate(100,100)">
            <!-- Shield background -->
            <path d="M -20,-15 L 20,-15 L 20,15 Q 0,25 -20,15 Z" fill="#1a5f7a" stroke="#c9a961" stroke-width="1"/>
            
            <!-- Eagle icon (simplified) -->
            <g transform="scale(0.8)" fill="#c9a961">
              <path d="M -8,-5 L -3,0 L -8,5 M 8,-5 L 3,0 L 8,5 M 0,-8 L 0,8" stroke="#c9a961" stroke-width="1.5" fill="none"/>
              <circle cx="0" cy="0" r="2" fill="#c9a961"/>
            </g>
          </g>
        </svg>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="login-form">
        <!-- Email input -->
        <div class="input-group">
          <span class="input-icon">👤</span>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="Enter your email"
            required
          />
        </div>

        <!-- Password input -->
        <div class="input-group">
          <span class="input-icon">🔐</span>
          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="Enter your password"
            required
          />
        </div>

        <!-- Error message -->
        <div v-if="authStore.error" class="error-message">
          {{ authStore.error }}
        </div>

        <!-- Login button -->
        <button
          type="submit"
          :disabled="authStore.loading"
          class="login-btn"
        >
          {{ authStore.loading ? 'Logging in...' : 'Log in' }}
        </button>
      </form>

      <!-- Forgot password link -->
      <div class="forgot-password">
        <a href="#" @click.prevent="handleForgotPassword">Forgot your password?</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: ''
})

const handleLogin = async () => {
  const success = await authStore.login(form.email, form.password)
  if (success) {
    router.push('/')
  }
}

const handleForgotPassword = () => {
  alert('Password reset feature coming soon. Please contact your administrator.')
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #0d5a73 0%, #0a4a5c 50%, #186b82 100%);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  position: relative;
  overflow: hidden;
}

/* Animated particles background */
.particles {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.particle {
  position: absolute;
  width: 4px;
  height: 4px;
  background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%;
  animation: float linear infinite;
}

.particle:nth-child(1) { width: 6px; height: 6px; left: 10%; top: 20%; animation-duration: 20s; }
.particle:nth-child(2) { width: 4px; height: 4px; left: 80%; top: 30%; animation-duration: 25s; }
.particle:nth-child(3) { width: 5px; height: 5px; left: 50%; top: 10%; animation-duration: 22s; }
.particle:nth-child(4) { width: 3px; height: 3px; left: 20%; top: 60%; animation-duration: 28s; }
.particle:nth-child(5) { width: 7px; height: 7px; left: 90%; top: 50%; animation-duration: 30s; }
.particle:nth-child(6) { width: 4px; height: 4px; left: 15%; top: 40%; animation-duration: 24s; }
.particle:nth-child(7) { width: 5px; height: 5px; left: 75%; top: 70%; animation-duration: 26s; }
.particle:nth-child(8) { width: 3px; height: 3px; left: 40%; top: 80%; animation-duration: 32s; }
.particle:nth-child(9) { width: 6px; height: 6px; left: 60%; top: 60%; animation-duration: 28s; }
.particle:nth-child(10) { width: 4px; height: 4px; left: 30%; top: 90%; animation-duration: 25s; }
.particle:nth-child(11) { width: 5px; height: 5px; left: 85%; top: 20%; animation-duration: 27s; }
.particle:nth-child(12) { width: 3px; height: 3px; left: 5%; top: 70%; animation-duration: 29s; }
.particle:nth-child(13) { width: 6px; height: 6px; left: 70%; top: 40%; animation-duration: 23s; }
.particle:nth-child(14) { width: 4px; height: 4px; left: 25%; top: 15%; animation-duration: 31s; }
.particle:nth-child(15) { width: 5px; height: 5px; left: 95%; top: 80%; animation-duration: 26s; }
.particle:nth-child(16) { width: 3px; height: 3px; left: 45%; top: 50%; animation-duration: 24s; }
.particle:nth-child(17) { width: 7px; height: 7px; left: 55%; top: 30%; animation-duration: 28s; }
.particle:nth-child(18) { width: 4px; height: 4px; left: 12%; top: 85%; animation-duration: 25s; }
.particle:nth-child(19) { width: 5px; height: 5px; left: 88%; top: 10%; animation-duration: 30s; }
.particle:nth-child(20) { width: 3px; height: 3px; left: 35%; top: 35%; animation-duration: 27s; }

@keyframes float {
  0% {
    opacity: 0;
    transform: translateY(100vh) translateX(0);
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 1;
  }
  100% {
    opacity: 0;
    transform: translateY(-100vh) translateX(100px);
  }
}

/* Login card */
.login-card {
  background: rgba(255, 255, 255, 0.98);
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 420px;
  padding: 50px 40px;
  position: relative;
  z-index: 10;
  backdrop-filter: blur(10px);
}

/* Logo section */
.logo-section {
  text-align: center;
  margin-bottom: 40px;
}

.navy-logo {
  width: 140px;
  height: 140px;
  margin: 0 auto;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

/* Login form */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Input groups */
.input-group {
  position: relative;
  display: flex;
  align-items: center;
  background: #f8f9fa;
  border: 2px solid #e0e4ea;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.input-group:focus-within {
  border-color: #1a5f7a;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(26, 95, 122, 0.1);
}

.input-icon {
  position: absolute;
  left: 14px;
  font-size: 18px;
  pointer-events: none;
}

.input-group input {
  flex: 1;
  padding: 14px 14px 14px 45px;
  border: none;
  background: transparent;
  font-size: 14px;
  color: #333;
  outline: none;
  font-family: inherit;
}

.input-group input::placeholder {
  color: #999;
}

/* Error message */
.error-message {
  background: #fee5e5;
  color: #c33;
  padding: 12px 14px;
  border-radius: 8px;
  font-size: 13px;
  text-align: center;
  border-left: 4px solid #c33;
  margin-top: 8px;
}

/* Login button */
.login-btn {
  background: linear-gradient(135deg, #003d5c 0%, #004a70 100%);
  color: white;
  border: none;
  padding: 14px 20px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 8px;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  box-shadow: 0 4px 12px rgba(0, 61, 92, 0.3);
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 61, 92, 0.4);
  background: linear-gradient(135deg, #004a70 0%, #005c8a 100%);
}

.login-btn:active:not(:disabled) {
  transform: translateY(0);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Forgot password link */
.forgot-password {
  text-align: center;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid #e0e4ea;
}

.forgot-password a {
  color: #1a5f7a;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.forgot-password a:hover {
  color: #0d4a5c;
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 480px) {
  .login-card {
    max-width: 100%;
    margin: 20px;
    padding: 40px 30px;
  }

  .navy-logo {
    width: 120px;
    height: 120px;
  }

  .login-btn {
    padding: 12px 16px;
    font-size: 14px;
  }
}
</style>
