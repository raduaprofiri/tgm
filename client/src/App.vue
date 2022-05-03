<template>
  <navbar></navbar>
  
  <div class="container my-5">
    <router-view></router-view>
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
</script>
