const fondo =  document.querySelector(".fondo");
const loginlink = document.querySelector(".login-link");
const registrarlink = document.querySelector(".registrar-link");
const btn = document.querySelector(".btn");

registrarlink.addEventListener("click", () => {
fondo.classList.add('active');
});

loginlink.addEventListener("click", () => {
    fondo.classList.remove('active');
});

btn.addEventListener("click", () => {
    fondo.classList.add('active-btn');
});

iconocerrar.addEventListener("click", () => {
    fondo.classList.remove('active-btn');
});

