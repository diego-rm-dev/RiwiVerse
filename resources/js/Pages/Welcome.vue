<script setup>
import { onMounted, ref, computed } from "vue";

onMounted(() => {
    const languageSelector = document.querySelector(".selected-language");
    const dropdown = document.querySelector(".dropdown");

    if (languageSelector && dropdown) {
        languageSelector.addEventListener("click", () => {
            const isDropdownVisible = dropdown.style.display === "block";
            dropdown.style.display = isDropdownVisible ? "none" : "block";
        });

        document.addEventListener("click", (event) => {
            if (!languageSelector.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });
    }
});

const redirectRegister = () => {
    window.location.href = "/register";
}

const defaultIcons = {
    like: '../../Icons/line/heart.svg',
};

const alternateIcons = {
    like: '../../Icons/line/heart-filled.svg',
};

const currentIcons = ref({ ...defaultIcons });

const toggleLike = () => {
    currentIcons.value.like =
        currentIcons.value.like === defaultIcons.like
            ? alternateIcons.like
            : defaultIcons.like;

    likeCount.value += currentIcons.value.like === alternateIcons.like ? 1 : -1;
};

const quotes = ref([
    { text: "Un bug al día mantiene al deploy feliz.", author: "- RiwiVerse" },
    { text: "Si no tienes bugs, ¿realmente programaste?", author: "- RiwiVerse" },
    { text: "Debuggear a las 3 AM no es un hábito, es un estilo de vida.", author: "- RiwiVerse" },
    { text: "Cuando todo falla, culpa al junior... o al senior.", author: "- RiwiVerse" },
    { text: "Un buen coder siempre encuentra un bug para memear.", author: "- RiwiVerse" },
    { text: "El código limpio es como un unicornio... todos hablan de él, nadie lo ha visto.", author: "- RiwiVerse" },
    { text: "La vida es como un while(true)... llena de loops infinitos.", author: "- RiwiVerse" },
    { text: "¿Frontend o backend? Yo elijo el stack overflow.", author: "- RiwiVerse" },
    { text: "En el código y en el amor: nunca confíes en un 'por siempre'.", author: "- RiwiVerse" },
    { text: "La programación es un arte: haces magia con bugs y esperanza.", author: "- RiwiVerse" },
    { text: "El mejor programador no es el que no tiene bugs, es el que sabe memearlos.", author: "- RiwiVerse" },
    { text: "Error 404: Mis ganas de seguir debuggeando no fueron encontradas.", author: "- RiwiVerse" },
    { text: "El código siempre compila... hasta que alguien lo prueba.", author: "- RiwiVerse" },
    { text: "¿Qué es peor que un bug? Un cliente que lo encuentra primero.", author: "- RiwiVerse" },
    { text: "El testing es opcional... hasta que el sistema se rompe.", author: "- RiwiVerse" },
    { text: "Un merge conflict al día mantiene la felicidad lejos.", author: "- RiwiVerse" },
    { text: "Escribir código es fácil, entenderlo después de 6 meses es el verdadero reto.", author: "- RiwiVerse" },
    { text: "Los viernes no son para deploys... ni para aventuras.", author: "- RiwiVerse" },
    { text: "No temas al código que falla, teme al código que no sabes por qué funciona.", author: "- RiwiVerse" },
    { text: "Un console.log es el mejor amigo del programador.", author: "- RiwiVerse" },
]);

const likeCount = ref(Math.floor(Math.random() * 1000) + 1);
const commentCount = ref(Math.floor(Math.random() * 500) + 1);
const shareCount = ref(Math.floor(Math.random() * 200) + 1);


const currentIndex = ref(0);

const currentQuote = computed(() => quotes.value[currentIndex.value]);

const startQuoteRotation = () => {
    setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % quotes.value.length;
    }, 5000);
};

onMounted(() => {
    startQuoteRotation();
});

</script>

<template>
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <img src="../../Icons/main-logo.svg" alt="Riwi-verse">
            </div>
            <div class="links">
                <ul class="lista-links">
                    <li><a href="/register">Registrarme</a></li>
                    <li>
                        <a href="/login" class="cta">
                            <span>Inicia Sesión</span>
                        </a>
                    </li>
                    <li class="language-selector">
                        <div class="selected-language">
                            Español <span>▼</span>
                        </div>
                        <ul class="dropdown">
                            <li><a href="#">Español</a></li>
                            <li><a href="#">English</a></li>
                        </ul>
                    </li>
                </ul>

            </div>
        </div>
        <div class="hero-banner">
            <div class="image-banner">
                <div class="image-banner-card">
                    <img src="../../Icons/imagen-CTA.svg" alt="Hero Banner" class="banner-image">
                </div>
            </div>
            <div class="banner-text-card">
                <div class="text-banner">
                    <h1 class="title-banner-text">¿Listo para compartir el bug más gracioso de tu vida?</h1>
                    <h2 class="subtitle-banner-text">En RiwiVerse los bugs no se arreglan, ¡se memean!</h2>
                    <p class="description-banner-text">Únete a nuestra comunidad y convierte esos errores de código en
                        risas compartidas. Porque en un mundo lleno de 404, aquí somos el 200 OK de los memes.</p>
                    <button v-on:click="redirectRegister" class="button-cta-banner-text">Quiero unirme a la
                        diversión</button>
                </div>
            </div>
        </div>
        <div class="information-section">
            <div class="first-information">
                <div class="programa-text">
                    <h1>PROGRAMA</h1>
                </div>
                <h1 class="h1-first-section">como un heroe</h1>
            </div>
            <div class="second-information">
                <div class="memea-text">
                    <h1>MEMEA</h1>
                </div>
                <h1 class="h1-second-section">como una leyenda</h1>
            </div>
        </div>
        <div class="comunity-section">
            <div class="comunity-section-title">
                <h1>"{{ currentQuote.text }}"</h1>
                <p class="quote-author">{{ currentQuote.author }}</p>
            </div>
        </div>

        <div class="demostration-section">
            <div class="post-card">
                <div class="post-header">
                    <div class="avatar">
                        <img src="../../Icons/perfil-1.jpg" alt="User Avatar" />
                    </div>
                    <div class="user-info">
                        <h3 class="username">Diego Ramirez</h3>
                        <span class="post-time">• 6 m. ago</span>
                    </div>
                </div>

                <div class="post-content">
                    <img src="../../Icons/meme-1.jpeg" alt="Meme Image" class="meme-image" />
                </div>

                <div class="post-actions">
                    <div class="action-group">
                        <span class="counter">{{ likeCount }}</span>
                        <button class="action-btn" aria-label="Like" @click="toggleLike">
                            <img :src="currentIcons.like" alt="Like" class="icon" />
                        </button>
                    </div>
                    <div class="action-group">
                        <span class="counter">{{ commentCount }}</span>
                        <button class="action-btn" aria-label="Comment">
                            <img src="../../../public/Icons/line/comment.svg" alt="Comment" class="icon" />
                        </button>
                    </div>
                    <div class="action-group">
                        <span class="counter">{{ shareCount }}</span>
                        <button class="action-btn" aria-label="Share">
                            <img src="../../../public/Icons/line/share.svg" alt="Share" class="icon" />
                        </button>
                    </div>
                </div>


                <div class="post-caption">
                    <p class="caption-text">que aprendan un lenguaje de hombre :v</p>
                    <div class="tags">
                        <span>#risa</span>
                        <span>#programming</span>
                        <span>#code</span>
                    </div>
                </div>

                <div class="post-comments">
                    <p>comentarios ↓</p>
                </div>
            </div>
            <div class="text-demostration">
                <h1 class="demo-title">¡Únete a la diversión en RiwiVerse!</h1>
                <p class="demo-text">
                    ¿Tienes un bug que merece ser memorizado? Aquí puedes:
                </p>
                <ul class="demo-list">
                    <li>Compartir tus memes más graciosos.</li>
                    <li>Votar por los reyes del humor coder.</li>
                    <li>Aprender de historias y consejos de otros programadores.</li>
                    <li>Conectar con una comunidad que ama tanto el código como los memes.</li>
                </ul>
                <p class="demo-footer">
                    <strong>Porque aquí los bugs no se arreglan… ¡se memean!</strong>
                </p>
                <div class="cta-container">
                    <button class="cta-button" v-on:click="redirectRegister">¡Regístrate ahora!</button>
                </div>
            </div>
        </div>
        <div class="biografy-section">
            <div class="bio-image-container">
                <div class="bio-image">
                    <img src="../../Icons/perfil-1.jpg" alt="Diego Ramirez" />
                </div>
                <div class="contact-info">
                    <div class="social-icons">
                        <a href="https://www.instagram.com/diego_rm_dev" target="_blank" aria-label="Instagram">
                            <img src="../../../public/Icons/line/instagram.svg" alt="Instagram Icon" />
                        </a>
                        <a href="https://x.com/diego_rm_dev" target="_blank" aria-label="Twitter">
                            <img src="../../../public/Icons/line/twitter.svg" alt="X (Twitter) Icon" />
                        </a>
                        <a href="https://www.linkedin.com/in/diego-andres-ramirez-mejia-1584a5323" target="_blank"
                            aria-label="LinkedIn">
                            <img src="../../../public/Icons/line/linkedin.svg" alt="LinkedIn Icon" />
                        </a>
                    </div>
                    <p class="email">diego.rm.dev@gmail.com</p>
                </div>
            </div>

            <div class="bio-text-container">
                <h2 class="bio-title">El Coder Detrás de RiwiVerse</h2>
                <p>¡Hola, soy Diego Ramirez! <br>
                    Soy un coder freelance desde Medellín, Colombia, al igual que tú: alguien que vive entre líneas de
                    código, sueños de deploys perfectos y, claro, uno que otro bug inesperado.</p>
                <h3>¿Mi Filosofía?</h3>
                <ul>
                    <li>Si el código no compila... ¡haz un meme!</li>
                    <li>Si encuentras un bug... ¡compártelo antes de arreglarlo!</li>
                    <li>Si todo funciona a la primera... bueno, eso nunca pasa, ¿cierto?</li>
                </ul>
                <p>Como tú, también he pasado noches largas peleando con errores misteriosos, y de ahí nació la idea de
                    RiwiVerse: un lugar donde todos los coders podamos reírnos de esas batallas (y ganar algo de cordura
                    en el proceso).</p>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>© 2024 RiwiVerse | Medellín, Colombia.</p>
    </footer>
</template>

<style>
.footer {
    background-color: #2c2c54;
    color: #fff;
    text-align: center;
    padding: 15px 20px;
    font-size: 0.9rem;
    font-family: Arial, sans-serif;
    height: 100px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
}


.biografy-section {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    background-color: #f9f9f9;
    padding: 100px;
    position: relative;
}

.bio-image-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    background-color: #2c2c54;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    color: #fff;
    max-width: 600px;
    z-index: 2;
    margin-top: -40px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.contact-info {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.social-icons {
    display: flex;
    gap: 15px;
}

.social-icons a {
    display: inline-block;
    width: 40px;
    height: 40px;
}

.social-icons img {
    width: 100%;
    height: 100%;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
    transition: transform 0.2s ease-in-out;
}

.social-icons a:hover img {
    transform: scale(1.2);
}

.email {
    font-size: 1.2rem;
    font-weight: bold;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.bio-image {
    position: relative;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    margin-bottom: 20px;
    z-index: 2;
    /* Asegura que esté encima de decoraciones */
}

.bio-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bio-text-container {
    flex: 1;
    padding: 40px 20px;
    background: linear-gradient(135deg, #6B5CFF 0%, #4A4AFF 100%);
    color: #fff;
    border-radius: 0 12px 12px 0;
    position: relative;
    z-index: 1;
    border-radius: 12px;
}

.bio-title {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.bio-text-container h3 {
    margin-top: 20px;
    font-size: 1.4rem;
    font-weight: bold;
}

.bio-text-container ul {
    list-style: disc;
    margin: 10px 0;
    padding-left: 20px;
}

.bio-text-container ul li {
    margin-bottom: 10px;
}

.bio-text-container p {
    line-height: 1.5;
    margin-bottom: 10px;
}


.demostration-section {
    display: flex;
    gap: 80px;
    padding: 20px;
    align-items: center;
    max-width: 900px;
    margin: 0 auto;
    justify-content: center;
}

.post-card {
    flex: 1;
}

.text-demostration {
    flex: 1;
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 12px;
    border: 2px solid #6b5cff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.demo-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #6b5cff;
    margin-bottom: 15px;
}

.demo-text {
    font-size: 1rem;
    color: #333;
    margin-bottom: 10px;
}

.demo-list {
    list-style: none;
    padding-left: 0;
    margin: 0 0 15px;
}

.demo-list li {
    font-size: 1rem;
    margin-bottom: 10px;
    color: #6b5cff;
    position: relative;
    padding-left: 20px;
}

.demo-list li::before {
    content: "✔";
    color: #ff2404;
    font-weight: bold;
    position: absolute;
    left: 0;
    top: 0;
}

.demo-footer {
    font-size: 1rem;
    color: #333;
    text-align: center;
    margin-top: 15px;
    font-weight: bold;
}


.cta-container {
    text-align: center;
    margin-top: 20px;
}

.cta-button {
    background-color: #6B5CFF;
    color: #fff;
    padding: 12px 24px;
    font-weight: 600;
    font-size: 1rem;
    transform: skewX(-10deg);
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.cta-button span {
    display: inline-block;
    transform: skewX(10deg);
}

.cta-button:hover {
    background-color: #5a4ed6;
    transform: skewX(-10deg) scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.post-card {
    border: 2px solid #6b5cff;
    border-radius: 12px;
    padding: 20px;
    max-width: 450px;
    margin: 20px auto;
    background-color: #fff;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-card:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.avatar img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid #6b5cff;
}

.user-info {
    margin-left: 10px;
}

.username {
    font-size: 1rem;
    font-weight: bold;
    color: #333;
}

.post-time {
    font-size: 0.8rem;
    color: #888;
}

.post-content {
    text-align: center;
    margin: 15px 0;
}

.meme-image {
    width: 100%;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.post-actions {
    display: flex;
    justify-content: space-around;
    margin: 15px 0;
}

.action-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.counter {
    font-size: 0.9rem;
    font-weight: bold;
    color: #6b5cff;
}

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
}

.action-btn:hover {
    transform: scale(1.2);
}

.icon {
    width: 24px;
    height: 24px;
}


.post-caption {
    text-align: center;
    margin: 15px 0;
}

.caption-text {
    font-size: 1rem;
    font-weight: 400;
    color: #333;
}

.tags span {
    margin-right: 8px;
    color: #6b5cff;
    font-weight: bold;
    font-size: 0.9rem;
}

.post-comments {
    text-align: center;
    font-size: 0.9rem;
    color: #6b5cff;
    margin-top: 10px;
}

.post-card:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}


* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html,
body {
    width: 100%;
    height: 100%;
    font-family: 'Arial', sans-serif;
}

.container {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 100%;
    gap: 30px;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 20px 40px;
    height: 100px;
}

.lista-links {
    display: flex;
    list-style: none;
    justify-content: center;
    align-items: center;
    gap: 30px;
    font-size: 1rem;
}

.lista-links li a {
    text-decoration: none;
    color: #000;
    font-weight: 500;
    padding: 8px 12px;
}

.lista-links li a.cta {
    background-color: #6B5CFF;
    color: #fff;
    border-radius: 5px;
    padding: 12px 24px;
    font-weight: 600;
    font-size: 1rem;
    transform: skewX(-10deg);
    transition: all 0.3s ease-in-out;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.lista-links li a.cta span {
    display: inline-block;
    transform: skewX(10deg);
}

.lista-links li a.cta:hover {
    background-color: #5a4ed6;
    transform: skewX(-10deg) scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    color: #fff;
}

.lista-links li a {
    color: #6B5CFF;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    padding: 10px 20px;
    transition: transform 0.3s ease-in-out, color 0.3s ease-in-out;
    display: inline-block;
}

.lista-links li a:hover {
    transform: scale(1.05);
    color: #5A4ED6;
}


.hero-banner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    width: 100%;
    padding: 20px 120px;
}

.text-banner {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.hero-banner .image-banner {
    width: 40%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-banner .image-banner-card {
    width: 100%;
    height: 350px;
    background-color: #6B5CFF;
    border-radius: 30px;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-banner .image-banner-card img {
    width: 100%;
    max-width: 900px;
    height: auto;
}

.hero-banner .banner-text-card {
    width: 60%;
    display: flex;
    flex-direction: column;
    background-color: #6B5CFF;
    color: #fff;
    padding: 30px 70px;
    border-radius: 30px;
}

.title-banner-text {
    font-size: 2.2rem;
    font-weight: 700;
}

.subtitle-banner-text {
    font-size: 1.4rem;
    font-weight: 500;
}

.description-banner-text {
    font-size: 1rem;
    line-height: 1.5;
}

.button-cta-banner-text {
    padding: 10px;
    border: 2px solid #fff;
    border-radius: 20px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    color: #fff;
    width: 50%;
    background-color: transparent;
    transition: all 0.3s ease-in-out;
    text-align: center;
}

.button-cta-banner-text:hover {
    background-color: #fff;
    color: #6B5CFF;
    border: 2px solid #6B5CFF;
    transform: scale(1.05);
}


.logo img {
    width: 200px;
    transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    cursor: pointer;
}

.logo img:hover {
    transform: scale(1.07);
    opacity: 0.9;
}



.language-selector {
    position: relative;
    cursor: pointer;
}

.selected-language {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 12px;
    background-color: #f5f5f5;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    color: #333;
    transition: background-color 0.3s ease;
}

.selected-language:hover {
    background-color: #e0e0e0;
}

.dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 5px;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    width: 100px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: none;
    z-index: 10;
}

.language-selector:hover .dropdown {
    display: block;
}

.dropdown li {
    list-style: none;
    padding: 8px 12px;
}

.dropdown li a {
    text-decoration: none;
    color: #333;
    font-size: 0.9rem;
    display: block;
    transition: color 0.3s ease;
}

.dropdown li:hover a {
    color: #6B5CFF;
    background-color: #f5f5f5;
    border-radius: 8px;
}

.information-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 20px;
    padding: 20px 120px;
    align-items: center;
    width: 100%;
}

.first-information,
.second-information {
    display: flex;
    gap: 10px;
    align-items: center;
}

.h1-first-section,
.h1-second-section {
    font-size: 2.5rem;
    font-weight: 800;
}

.programa-text {
    background-color: #6B5CFF;
    color: #fff;
    padding: 10px 20px;
    font-weight: 600;
    transform: skewX(-10deg);
    border-radius: 5px;
    transition: all 0.3s ease-in-out;

}

.programa-text:hover {
    background-color: #5A4ED6;
    transform: skewX(-10deg) scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.programa-text h1 {
    font-size: 2.5rem;
    color: #fff;
    font-weight: 800;
}

.memea-text {
    background-color: #FE654F;
    color: #fff;
    padding: 10px 20px;
    font-weight: 600;
    transform: skewX(-10deg);
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
}

.memea-text:hover {
    background-color: #fd3c1e;
    transform: skewX(-10deg) scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

}

.memea-text h1 {
    font-size: 2.5rem;
    color: #fff;
    font-weight: 800;
}

.comunity-section {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    padding: 40px 120px;
    background-color: #6B5CFF;
    color: #fff;
    position: relative;
    overflow: hidden;
}

.comunity-section-title {
    font-size: 2.5rem;
    font-weight: 800;
    text-align: center;
    position: relative;
    z-index: 2;
    padding: 20px;
    text-transform: uppercase;
}

.comunity-section-title h1 {
    margin-bottom: 10px;
}

.quote-author {
    font-size: 1.2rem;
    font-weight: 600;
    color: #FFD166;
    margin-top: 10px;
    text-transform: none;
}

.comunity-section-title::before,
.comunity-section-title::after {
    content: "";
    position: absolute;
    width: 50px;
    height: 6px;
    background-color: #FE654F;
    top: 50%;
    transform: translateY(-50%);
}

.comunity-section-title::before {
    left: -70px;
}

.comunity-section-title::after {
    right: -70px;
}

.comunity-section::before {
    content: "";
    position: absolute;
    top: -30px;
    left: -30px;
    width: 100px;
    height: 100px;
    background-color: #FE654F;
    transform: rotate(45deg);
    opacity: 0.4;
    z-index: 1;
}

.comunity-section::after {
    content: "";
    position: absolute;
    bottom: -30px;
    right: -30px;
    width: 150px;
    height: 150px;
    background-color: #FFD166;
    transform: rotate(-45deg);
    opacity: 0.3;
    z-index: 1;
}
</style>
