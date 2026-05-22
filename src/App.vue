<script setup>
import { useRouter } from "vue-router"
import { useAuthStore } from "./stores/auth"
import "./styles/dark-theme.css"
</script>

<template>
  <router-view />
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body, #app {
  height: 100%;
  width: 100%;
  background: #0e1419;
  color: #e8eef2;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
</style>
