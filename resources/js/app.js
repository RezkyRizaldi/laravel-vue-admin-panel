import "./bootstrap";
import "admin-lte/plugins/bootstrap/js/bootstrap.bundle.min";
import "admin-lte/dist/js/adminlte.min";
import { createApp } from "vue/dist/vue.esm-bundler";
import { createRouter, createWebHistory } from "vue-router";
import routes from "./routes";

const app = createApp({});
const router = createRouter({
	routes,
	history: createWebHistory(),
});

app.use(router).mount("#app");
