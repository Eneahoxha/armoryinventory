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
        <img src="/navy-seal-logo.png" alt="U.S. Navy" class="navy-logo" />
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
  background: linear-gradient(135deg, #1a5f7a 0%, #2d7f5e 50%, #1a4a3d 100%);
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
  background: radial-gradient(circle, rgba(74, 157, 111, 0.6) 0%, rgba(74, 157, 111, 0) 70%);
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
  background: rgba(20, 30, 35, 0.95);
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
  width: 100%;
  max-width: 420px;
  padding: 50px 40px;
  position: relative;
  z-index: 10;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(74, 157, 111, 0.2);
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
  filter: drop-shadow(0 4px 12px rgba(74, 157, 111, 0.3));
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
  background: rgba(255, 255, 255, 0.05);
  border: 1.5px solid rgba(74, 157, 111, 0.3);
  border-radius: 10px;
  transition: all 0.3s ease;
}

.input-group:focus-within {
  border-color: #4a9d6f;
  background: rgba(74, 157, 111, 0.1);
  box-shadow: 0 0 0 3px rgba(74, 157, 111, 0.15);
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
  color: #e8eef2;
  outline: none;
  font-family: inherit;
}

.input-group input::placeholder {
  color: #a0adb5;
}

/* Error message */
.error-message {
  background: rgba(200, 50, 50, 0.15);
  color: #ff6b6b;
  padding: 12px 14px;
  border-radius: 8px;
  font-size: 13px;
  text-align: center;
  border-left: 4px solid #ff6b6b;
  margin-top: 8px;
}

/* Login button */
.login-btn {
  background: linear-gradient(135deg, #2d7f5e 0%, #1a5f7a 100%);
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
  box-shadow: 0 4px 12px rgba(45, 127, 94, 0.4);
  border: 1px solid rgba(74, 157, 111, 0.4);
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(45, 127, 94, 0.6);
  background: linear-gradient(135deg, #3a8f6f 0%, #2a6f8a 100%);
  border-color: rgba(74, 157, 111, 0.6);
}

.login-btn:active:not(:disabled) {
  transform: translateY(0);
}

.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Forgot password link */
.forgot-password {
  text-align: center;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid rgba(74, 157, 111, 0.2);
}

.forgot-password a {
  color: #4a9d6f;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.forgot-password a:hover {
  color: #6ab88f;
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
