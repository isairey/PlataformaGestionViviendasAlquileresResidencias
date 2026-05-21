import "./bootstrap";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

import "@phosphor-icons/web/bold";
import "@phosphor-icons/web/fill";
import "@phosphor-icons/web/regular";

// import "flowbite";
import { DataTable } from "simple-datatables";
import autoAnimate from "@formkit/auto-animate";

import.meta.glob("../assets/**/*");
// import.meta.glob(["../assets/fonts/**/*", "../assets/images/**/*"]);

window.Alpine = Alpine;
window.DataTable = DataTable;
window.autoAnimate = autoAnimate;

Alpine.plugin(collapse);
Alpine.start();
