<template>
  <div>
    <h1 class="text-center my-5">Login</h1>

    <div class="form-outline mb-4">
      <label class="form-label" for="email">Email address</label>
      <input type="email" id="email" class="form-control" v-model="email" />
    </div>

    <div class="form-outline mb-4">
      <label class="form-label" for="password">Password</label>

      <input
        type="password"
        id="password"
        class="form-control"
        v-model="password"
      />
    </div>

    <button
      type="button"
      class="btn btn-primary btn-block mb-4"
      v-on:click="login"
    >
      Sign in
    </button>
  </div>
</template>

<script>
import { ref } from "vue";
import axios from 'axios';

axios.defaults.withCredentials = true;

export default {
  setup() {
    const email = ref('raduaprofiri@gmail.com');
    const password = ref('secret');

    const login = () => {
        axios.get('http://localhost/sanctum/csrf-cookie')
            .then(() => {
                axios.post('http://localhost/login', {
                    email: email.value,
                    password: password.value
                })
                .then(() => {
                    window.location.href = '/';
                })
                .catch(() => {
                    alert('Invalid credentials');
                });
            });
    };

    return {
      email,
      password,
      login,
    };
  },
};
</script>
