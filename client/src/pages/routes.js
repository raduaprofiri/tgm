import { createRouter } from "vue-router";
import Homepage from "./items/Index.vue";
import SignIn from "./auth/Login.vue";

const routes = [
  { path: "/", component: Homepage },
  { path: "/sign-in/", component: SignIn },
];

export default function (history) {
  return createRouter({
    history,
    routes,
  });
}
