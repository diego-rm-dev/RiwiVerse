<script setup>
/* =========================================================
 * 1) Imports
 * ========================================================= */
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import NavLink from '../Components/NavLink.vue'
import axios from 'axios';



/* =========================================================
 * 2) Referencias, Variables Reactivas y Objetos
 * ========================================================= */
const isNavbarVisible = ref(true)
let lastScrollY = 0

const newCommentText = ref({})

const currentImageIndex = ref({})

const defaultIcons = {
    like: "Icons/line/heart.svg",
}

const currentIcons = {
    like: "Icons/line/heart-filled.svg",
}

const showNotifications = ref(false)

const showModal = ref(false)

/**
 * Objeto que representa un nuevo Post
 */
const post = ref({
    title: "",
    text: "",
    hashtags: "",
    status: "published",
    images: [],
})

const navigation = [
    { name: 'Home', href: '/', current: true },
    { name: 'Sobre Nosotros', href: '/about', current: false },
    { name: 'Seguidores', href: '/followers', current: false },
]


/* =========================================================
 * 3) Desestructuración de usePage
 * ========================================================= */
const { props } = usePage()
const user = props.auth.user


/* =========================================================
 * 4) Computed Properties
 * ========================================================= */

const publications = ref([...props.publications]);
const notifications = computed(() => props.notifications || [])
const Comments = computed(() => props.comments || [])
const displayedPublications = ref([]); // Publicaciones actualmente visibles
const itemsPerPage = 5; // Cantidad de publicaciones por página
let currentPage = ref(1); // Página actual

const commentsPerPage = 3; // Cantidad de comentarios por página
const currentCommentPage = ref({}); // Página actual para cada publicación


/** funcion para calcular comentarios visibles */


/**
 * Ordena los comentarios de un post por fecha de manera descendente
 */
function sortedComments(post) {
    return [...(post.comments || [])].sort((a, b) => {
        return new Date(b.date) - new Date(a.date)
    })
}

function paginatedComments(post) {
    const page = currentCommentPage.value[post.id] || 1;
    const startIndex = (page - 1) * commentsPerPage;
    const endIndex = startIndex + commentsPerPage;

    return sortedComments(post).slice(startIndex, endIndex);
}

function loadMoreComments(post) {
    if (!currentCommentPage.value[post.id]) {
        currentCommentPage.value[post.id] = 1;
    }
    currentCommentPage.value[post.id]++;
}


function hasMoreComments(post) {
    const page = currentCommentPage.value[post.id] || 1;
    const totalComments = sortedComments(post).length;

    return page * commentsPerPage < totalComments;
}

function resetCommentsPage(post) {
    if (!post || !post.id) return;

    // Restablece la página actual de comentarios para esta publicación
    currentCommentPage.value[post.id] = 1;

    logInfo("Comentarios reiniciados al inicio", {
        postId: post.id,
        currentPage: currentCommentPage.value[post.id],
    });
}




/* =========================================================
 * 5) Funciones y Métodos
 * ========================================================= */

/**
 * Formatea una fecha en formato ISO a formato MySQL
 */
function formatDateForMySQL(isoDate) {
    const date = new Date(isoDate)
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')}`
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', {
        dateStyle: 'long',
        timeStyle: 'short',
    }).format(date);
}

/**
 * Redirecciona a la publicación
 */
function redirectPublication(publicationId) {
    router.push(`/publications/${publicationId}`)
}

/**
 * Activa o desactiva el 'me gusta' de un post
 */
function toggleLike(post) {
    if (!post || !post.id) {
        console.error("El post no es válido o no tiene un ID.");
        return;
    }

    const originalState = post.isLiked;
    const originalLikesCount = post.likesCount;

    post.isLiked = !post.isLiked;
    post.likesCount += post.isLiked ? 1 : -1;

    router.post('/toggle-like', {
        publication_id: post.id
    }, {
        preserveScroll: true, // Evita que Inertia haga scroll al inicio de la página
        onSuccess: () => {
            // No hacemos nada adicional porque ya actualizamos el estado local
        },
        onError: () => {
            post.isLiked = originalState;
            post.likesCount = originalLikesCount;
            console.error("Error al alternar el like.");
        }
    });
}


/**
 * Recalcula cuáles publicaciones se muestran 
 * según la página actual (currentPage).
 */
function recalcDisplayedPublications() {
    const startIndex = (currentPage.value - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;

    displayedPublications.value = publications.value.slice(startIndex, endIndex);

    logInfo("displayedPublications recalculado", {
        currentPage: currentPage.value,
        displayedPublications: displayedPublications.value,
    });
}





/**
 * Función para ir a una página específica y recalcular publicaciones.
 */
function goToPage(page) {
    currentPage.value = page;

    // Recalcular las publicaciones visibles
    recalcDisplayedPublications();

    logInfo("Página cambiada y publicaciones recalculadas", {
        currentPage: page,
        displayedPublications: displayedPublications.value,
    });
}

const totalPages = Math.ceil(publications.value.length / itemsPerPage);
if (currentPage.value === totalPages) {
    goToPage(totalPages);
}


/**
 * Guardar y restaurar la página en localStorage
 */

// Cuando cambie currentPage, guardamos la página en localStorage
watch(currentPage, (newVal) => {
    localStorage.setItem('currentPage', newVal);
    logInfo("Página actual guardada en localStorage", newVal);
});



/**
 * Envía un comentario (texto) a la API
 */
/**
 * Envía un comentario (texto) a la API
 */
function submitComment(post) {
    if (!newCommentText.value[post.id]) {
        newCommentText.value[post.id] = '';
    }

    const text = newCommentText.value[post.id].trim();
    if (!text) return;

    const formData = {
        text,
        status: 'published',
        publication_id: post.id,
        date: formatDateForMySQL(new Date().toISOString()),
    };

    axios.post('/comments', formData)
        .then((response) => {
            const newComment = response.data.comment;

            // Encuentra la publicación específica
            const publicationIndex = publications.value.findIndex((p) => p.id === post.id);
            if (publicationIndex !== -1) {
                // Crea un nuevo array para los comentarios para asegurar reactividad
                publications.value[publicationIndex].comments = [
                    newComment,
                    ...(publications.value[publicationIndex].comments || []),
                ];
            }

            // Limpia el campo de texto
            newCommentText.value[post.id] = '';
        })
        .catch((errors) => {
            console.error("Error al intentar agregar un comentario", errors);
        });
}

/**
 * Maneja la navegación de imágenes (Siguiente)
 */
const nextImage = (postId) => {
    const images = publications.value.find((post) => post.id === postId)?.images || []
    if (images.length > 1) {
        currentImageIndex.value[postId] =
            (currentImageIndex.value[postId] + 1) % images.length || 0
    }
}

/**
 * Maneja la navegación de imágenes (Anterior)
 */
const prevImage = (postId) => {
    const images = publications.value.find((post) => post.id === postId)?.images || []
    if (images.length > 1) {
        currentImageIndex.value[postId] =
            (currentImageIndex.value[postId] - 1 + images.length) % images.length || 0
    }
}

/**
 * Maneja la subida de imágenes en un nuevo Post
 */
const handleImageUpload = (event) => {
    const files = event.target.files

    Array.from(files).forEach((file) => {
        const reader = new FileReader()
        reader.onload = (e) => {
            post.value.images.push({
                file: file,
                preview: e.target.result,
            })
        }
        reader.readAsDataURL(file)
    })
}

/**
 * Envía el Post (título, texto, imágenes...) a la API
 */

function submitPost() {
    const formData = new FormData();

    formData.append("title", post.value.title);
    formData.append("text", post.value.text);
    formData.append("hashtags", post.value.hashtags);
    formData.append("status", post.value.status);

    post.value.images.forEach((image, index) => {
        formData.append(`images[${index}]`, image.file);
    });

    showModal.value = false; // Cierra el modal

    router.post("/publications", formData, {
        preserveScroll: true,
        onSuccess: (response) => {
            const newPost = response.props.publications.at(-1); // Última publicación recibida del servidor

            // 1. Agregar la publicación al final del array principal
            publications.value.push(newPost);

            // 2. Actualizar las publicaciones visibles
            recalcDisplayedPublications();

            // 3. Asegurar que el scroll no se vea afectado
            logInfo("Nueva publicación añadida y recalculadas publicaciones visibles.", {
                newPost,
                displayedPublications: displayedPublications.value,
            });
        },
        onError: (errors) => {
            logError("Error al intentar publicar", errors);
        },
    });
}

/**
 * Muestra u oculta el panel de notificaciones
 */
function toggleNotifications() {
    showNotifications.value = !showNotifications.value
}

/**
 * Logout function
 */
const logout = () => {
    router.post(route('logout'))
}

/**
 * Muestra/Oculta la navbar al hacer scroll
 */
function handleScroll() {
    const currentScrollY = window.scrollY
    if (currentScrollY > lastScrollY) {
        isNavbarVisible.value = false
    } else {
        isNavbarVisible.value = true
    }
    lastScrollY = currentScrollY
}

/** Funcion para devolver hacia arriba el scroll */
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: "smooth", // Scroll suave
    });
}

function resetPublicationsPage() {
    currentPage.value = 1; // Restablecer la página actual a la primera
    recalcDisplayedPublications(); // Recalcular las publicaciones visibles
    logInfo("Publicaciones reiniciadas al inicio", { currentPage: currentPage.value });
}


/* =========================================================
 * 7) Inicializaciones y Logs
 * ========================================================= */
publications.value.forEach((post) => {
    currentImageIndex.value[post.id] = 0
})

console.log("Publications data:", publications.value)
console.log(Comments.value)

/* Funcion para cargar publicaciones paginadas*/

function loadMorePublications() {
    // Avanzamos a la siguiente página
    currentPage.value++;

    // Recalculamos las publicaciones mostradas
    recalcDisplayedPublications();

    // Hacemos scroll hacia arriba (opcional)
    scrollToTop();
}




/* Comprobacion para saber si hay mas publicaciones en cola */

const hasMorePublications = computed(() => {
    return currentPage.value * itemsPerPage <= publications.value.length;
});

/* =========================================================
 * 6) Ciclo de vida (Lifecycle Hooks)
 * ========================================================= */
onMounted(() => {
    loadMorePublications();
    logInfo("Componente montado");
    window.addEventListener('scroll', handleScroll);

    const savedPage = parseInt(localStorage.getItem('currentPage') || '1', 10);
    currentPage.value = savedPage;
    logInfo("Página actual restaurada", currentPage.value);

    recalcDisplayedPublications();
    logInfo("displayedPublications inicializado", displayedPublications.value);

    const savedScroll = localStorage.getItem('scrollPosition');
    if (savedScroll) {
        window.scrollTo(0, parseInt(savedScroll, 10));
        logInfo("Posición del scroll restaurada", savedScroll);
    }
});

onBeforeUnmount(() => {
    localStorage.setItem('scrollPosition', window.scrollY);
})

/** Loggers */

function logInfo(message, data = null) {
    console.log(`INFO: ${message}`, data);
}

function logError(message, error = null) {
    console.error(`ERROR: ${message}`, error);
}

window.onerror = function (message, source, lineno, colno, error) {
    logError("Error global capturado", { message, source, lineno, colno, error });
};

</script>

<template>
    <!-- 
        Componente principal que utiliza 'Disclosure' de HeadlessUI para
        generar un menú responsive (navbar) y controlar su comportamiento
        (abierto o cerrado).
        El nav está enlazado a 'isNavbarVisible' (del script) para mostrarse
        u ocultarse según la posición de scroll.
    -->
    <Disclosure as="nav" class="bg-[#161B33]" v-slot="{ open }">
        <!-- 
            Barra de navegación fija en la parte superior de la página
            Se muestra u oculta mediante la clase dinámica que usa isNavbarVisible.
            Esto está controlado por la función handleScroll() en el script.
        -->
        <nav class="fixed w-full z-30 bg-[#161B33] transition-transform duration-300" :class="{
            '-translate-y-full': !isNavbarVisible,
            'translate-y-0': isNavbarVisible
        }">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8 ml-[250px]">
                <div class="relative flex h-16 items-center justify-between">

                    <!-- 
                        Botón para el menú mobile (sólo se muestra en pantallas pequeñas).
                        Utiliza 'DisclosureButton' y cambia el ícono de menu (Bars3Icon) o close (XMarkIcon)
                        según el estado 'open' de Disclosure.
                    -->
                    <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                        <DisclosureButton
                            class="relative inline-flex items-center justify-center rounded-md p-2 text-white hover:bg-[#3A3D62] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!open" class="block size-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block size-6" aria-hidden="true" />
                        </DisclosureButton>
                    </div>

                    <!--
                        Sección principal del navbar:
                        - Muestra los enlaces de navegación iterando sobre el array 'navigation' 
                          definido en el script.
                        - Cada enlace verifica si está "current" para aplicar estilos distintos.
                    -->
                    <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                        <div class="hidden sm:ml-6 sm:block">
                            <div class="flex space-x-4">
                                <a v-for="item in navigation" :key="item.name" :href="item.href" :class="[
                                    item.current
                                        ? 'bg-[#6B5CFF] text-white'
                                        : 'text-[#F4F4F9] hover:bg-[#3A3D62]',
                                    'rounded-md px-3 py-2 text-sm font-medium'
                                ]" :aria-current="item.current ? 'page' : undefined">
                                    {{ item.name }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!--
                        Sección de iconos y acciones del usuario (notificaciones, perfil, etc.).
                        - El botón de notificaciones llama a la función toggleNotifications() del script 
                          para alternar la variable showNotifications.
                        - showNotifications se usa para mostrar/ocultar el panel de notificaciones.
                        - Se muestra el perfil del usuario ('user' del script) y su foto de perfil.
                    -->
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0 gap-3">

                        <!-- Botón de notificaciones -->
                        <button type="button"
                            class="relative ml-3 rounded-full bg-[#161B33] p-1 text-[#F4F4F9] hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#161B33]"
                            @click="toggleNotifications">
                            <span class="sr-only">View notifications</span>
                            <BellIcon class="size-6" aria-hidden="true" />
                        </button>

                        <!--
                            Panel de notificaciones (transition con nombre 'fade').
                            Se muestra cuando showNotifications == true.
                            Muestra la lista de 'notifications' (prop del script).
                            Cada notificación puede redirigir a la publicación correspondiente
                            usando la función redirectPublication(notif.data.publication_id) del script.
                        -->
                        <transition name="fade">
                            <div v-if="showNotifications"
                                class="absolute right-0 mt-2 w-80 bg-white rounded shadow-lg z-50 p-4"
                                style="top: 60px;">
                                <h3 class="text-lg font-semibold mb-2 text-[#161B33]">
                                    Notificaciones
                                </h3>

                                <!-- Lista de notificaciones -->
                                <div v-if="notifications.length > 0"
                                    class="flex flex-col gap-2 max-h-60 overflow-y-auto">
                                    <div v-for="(notif, idx) in notifications" :key="notif.id || idx"
                                        class="border-b border-gray-200 py-2 cursor-pointer"
                                        @click="redirectPublication(notif.data.publication_id)">
                                        <p class="text-sm text-gray-800">
                                            {{ notif.data.message || 'Sin mensaje' }}
                                        </p>
                                        <small class="text-xs text-gray-500">
                                            {{ new Date(notif.created_at).toLocaleString() }}
                                        </small>
                                    </div>
                                </div>

                                <div v-else class="text-gray-500 text-sm">
                                    No tienes notificaciones
                                </div>

                                <!-- Cerrar el panel de notificaciones -->
                                <button
                                    class="mt-3 w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-500 text-sm"
                                    @click="showNotifications = false">
                                    Cerrar
                                </button>
                            </div>
                        </transition>

                        <!--
                            Dropdown de perfil (Menu de HeadlessUI).
                            Muestra la foto de perfil del usuario y su nombre.
                            Aquí no está desplegando opciones de menú adicional,
                            pero está preparado para ello.
                        -->
                        <Menu as="div" class="relative ml-3">
                            <div>
                                <MenuItem
                                    class="relative flex rounded-full bg-[#161B33] text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#161B33]">
                                <img class="size-8 rounded-full" :src="user.profile_photo_url" alt=" User Profile" />
                                </MenuItem>
                            </div>

                            <!--
                                Transition para mostrar/ocultar opciones de menú 
                                (sin contenido adicional por ahora).
                            -->
                            <transition enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                            </transition>
                        </Menu>

                        <!--
                            Nombre del usuario logueado (viene desde props.auth.user en el script).
                        -->
                        <p class="text-white">
                            {{ user.name }}
                        </p>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 
            Modal para crear nuevas publicaciones (se muestra cuando showModal == true).
            - Usa un formulario que llama a submitPost() (del script) en el evento @submit.prevent.
            - handleImageUpload() se usa para procesar las imágenes.
        -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl h-[80vh] p-6 relative flex flex-col">
                <!-- Botón para cerrar el modal -->
                <button @click="showModal = false" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
                    ✖
                </button>

                <h2 class="text-lg font-bold mb-4">Crear Publicación</h2>

                <!-- Contenido desplazable (scrollable) para el formulario -->
                <div class="flex-grow overflow-y-auto pr-2">
                    <form @submit.prevent="submitPost" class="grid grid-cols-2 gap-6">
                        <!-- Título del post -->
                        <div class="col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input v-model="post.title" id="title" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Escribe un título..." required />
                        </div>

                        <!-- Descripción del post -->
                        <div class="col-span-2">
                            <label for="text" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea v-model="post.text" id="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                rows="4" placeholder="Escribe algo interesante..." required></textarea>
                        </div>

                        <!-- Hashtags -->
                        <div class="col-span-2">
                            <label for="hashtags" class="block text-sm font-medium text-gray-700">Hashtags</label>
                            <input v-model="post.hashtags" id="hashtags" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ejemplo: #coding, #memes, #bugs" />
                        </div>

                        <!-- Subir imágenes (file input) -->
                        <div>
                            <label for="images" class="block text-sm font-medium text-gray-700">Subir imágenes</label>
                            <input id="images" type="file" multiple accept="image/*" @change="handleImageUpload"
                                class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-[#6B5CFF] file:text-white hover:file:bg-[#4A4AFF]" />
                        </div>

                        <!-- Vista previa de imágenes subidas -->
                        <div v-if="post.images.length" class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Vista previa de imágenes:</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <img v-for="(image, index) in post.images" :key="index" :src="image.preview"
                                    class="rounded-lg shadow-md object-cover w-full h-32" />
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Botón para publicar, llama a la función submitPost() -->
                <div class="mt-4 flex justify-end">
                    <button type="submit" v-on:click="submitPost"
                        class="bg-[#6B5CFF] text-white px-4 py-2 rounded-md hover:bg-[#4A4AFF]">
                        Publicar
                    </button>
                </div>
            </div>
        </div>

        <!-- 
            Menú móvil (panel desplegable) que aparece al presionar el botón en pantallas pequeñas.
            Usa DisclosurePanel de HeadlessUI y recorre los mismos enlaces 'navigation' para mostrarlos.
        -->
        <DisclosurePanel class="sm:hidden">
            <div class="space-y-1 px-2 pb-3 pt-2">
                <DisclosureButton v-for="item in navigation" :key="item.name" as="a" :href="item.href" :class="[
                    item.current
                        ? 'bg-[#6B5CFF] text-white'
                        : 'text-[#F4F4F9] hover:bg-[#3A3D62]',
                    'block rounded-md px-3 py-2 text-base font-medium'
                ]" :aria-current="item.current ? 'page' : undefined">
                    {{ item.name }}
                </DisclosureButton>
            </div>
        </DisclosurePanel>
    </Disclosure>

    <!-- 
        Barra lateral (sidebar) fija. Contiene:
        - Logo
        - Botón para abrir el modal (showModal = true)
        - Enlace al perfil (usa componente NavLink y route('profile.show'))
        - Botón de Logout que llama a la función logout() del script.
    -->
    <aside class="my-sidebar">
        <!-- Logo o título (opcional) -->
        <div class="sidebar-logo d-flex align-items-center justify-center">
            <div class="flex shrink-0 items-center">
                <img class="h-6 w-auto" src="../../Icons/logo-dark.svg" alt="RiwiVerse Logo" />
            </div>
        </div>

        <!-- Botón para publicar (abre el modal) -->
        <div class="sidebar-section">
            <button @click="showModal = true" class="sidebar-btn publish-btn">
                Publicar
            </button>
        </div>

        <!-- Botón para ir al perfil -->
        <div class="sidebar-section">
            <NavLink :href="route('profile.show')" class="sidebar-btn publish-btn">
                Profile
            </NavLink>
        </div>

        <!-- Separador flexible para empujar el logout al final -->
        <div class="sidebar-spacer"></div>

        <!-- Botón de Logout (llama a logout()) -->
        <div class="sidebar-logout">
            <button @click="logout" class="sidebar-btn logout-btn">
                Logout
            </button>
        </div>
    </aside>

    <!-- 
        Contenedor de publicaciones:
        - Recorremos 'publications' (computed en el script) con un v-for.
        - Mostramos cada publicación (post).
        - Control de imágenes (carrusel) usando currentImageIndex y las funciones nextImage() / prevImage().
        - Botón de like con toggleLike(post).
        - Sección de comentarios, con sortedComments(post) para ordenarlos,
          y submitComment(post) para enviar uno nuevo.
    -->
    <div class="publications-container">
        <!-- Columna de Posts -->
        <div class="posts-column">
            <div v-for="post in displayedPublications" :key="post.id" class="post-card">
                <!-- Header con avatar y nombre de usuario -->
                <div class="post-header">
                    <div class="avatar">
                        <img :src="post.user.profile_photo_url || 'Icons/line/user.svg'" alt="User Avatar" />
                    </div>
                    <div class="user-info">
                        <h3 class="username">
                            {{ post.user.name }}
                        </h3>
                        <span class="post-time">
                            {{ formatDate(post.created_at || post.date) }}
                        </span>
                    </div>
                </div>

                <!-- 
                    Content de la publicación:
                    - Si hay más de 1 imagen, se muestra un pequeño carrusel con botones 'Prev' y 'Next' 
                      que usan prevImage(post.id) y nextImage(post.id).
                    - Si hay exactamente 1 imagen, se muestra esa imagen única.
                -->
                <div v-if="post.images.length > 1" class="post-content">
                    <div class="carousel">
                        <img :src="post.images[currentImageIndex[post.id]]" alt="Post Image" class="carousel-image" />
                        <div class="carousel-controls">
                            <button @click="prevImage(post.id)" class="carousel-btn">
                                ◀
                            </button>
                            <button @click="nextImage(post.id)" class="carousel-btn">
                                ▶
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else-if="post.images.length === 1" class="post-content">
                    <img :src="post.images[0]" alt="Post Image" class="meme-image" />
                </div>

                <!-- 
                    Acciones del post (like, comment, share):
                    - Botón Like llama a toggleLike(post) 
                      usando la lógica del script para incrementar/decrementar el contador.
                    - Se muestra el contador de comentarios (post.comments.length).
                    - Se muestra un contador de shares (por ahora sin función).
                -->
                <div class="post-actions">
                    <div class="action-group">
                        <span class="counter">
                            {{ post.likesCount || 0 }}
                        </span>
                        <button class="action-btn" aria-label="Like" @click.prevent="toggleLike(post)">
                            <img :src="post.isLiked ? currentIcons.like : defaultIcons.like" alt="Like" class="icon" />
                        </button>
                    </div>

                    <div class="action-group">
                        <span class="counter">
                            {{ post.comments?.length || 0 }}
                        </span>
                        <button class="action-btn" aria-label="Comment">
                            <img src="/Icons/line/comment.svg" alt="Comment" class="icon" />
                        </button>
                    </div>

                    <div class="action-group">
                        <span class="counter">
                            {{ post.shares || 0 }}
                        </span>
                        <button class="action-btn" aria-label="Share">
                            <img src="/Icons/line/share.svg" alt="Share" class="icon" />
                        </button>
                    </div>
                </div>

                <!-- 
                    Texto y hashtags del post:
                    - Título y descripción.
                    - Se hace split de post.hashtags para mostrarlos como etiquetas.
                -->
                <div class="post-caption">
                    <p class="caption-text font-bold">
                        {{ post.title }}
                    </p>
                    <p class="caption-text">
                        {{ post.text }}
                    </p>
                    <div class="tags">
                        <span v-for="(tag, index) in post.hashtags.split(',')" :key="index">
                            #{{ tag.trim() }}
                        </span>
                    </div>
                </div>

                <!-- 
                    Sección de comentarios:
                    - sortedComments(post) ordena los comentarios según la fecha (desc).
                    - Se muestran los 3 más recientes (slice(0,3)).
                    - newCommentText[post.id] y submitComment(post) para enviar un nuevo comentario.
                -->
                <div class="comments-section" v-if="post.comments">
                    <h3 class="comments-title">Comentarios</h3>

                    <!-- Lista de comentarios paginados -->
                    <div class="comments-list">
                        <div v-for="(comment, idx) in paginatedComments(post)" :key="comment.id" class="comment-item">
                            <div class="comment-header">
                                <img :src="comment.user?.profile_photo_url || 'Icons/line/user.svg'" alt="avatar"
                                    class="comment-avatar" />
                                <div>
                                    <span class="comment-username">{{ comment.user?.name }}</span>
                                    <span class="comment-time">{{ comment.created_at }}</span>
                                </div>
                            </div>
                            <p class="comment-text">{{ comment.text }}</p>
                        </div>
                    </div>

                    <!-- Botón para cargar más comentarios o mensaje de fin -->
                    <div class="comments-pagination">
                        <button v-if="hasMoreComments(post)" class="load-more-comments" @click="loadMoreComments(post)">
                            Ver más comentarios
                        </button>
                        <p v-else class="no-more-comments">No hay más comentarios</p>
                        <button v-if="(currentCommentPage[post.id] || 1) > 1" class="reset-comments"
                            @click="resetCommentsPage(post)">
                            Volver al inicio de los comentarios
                        </button>
                    </div>

                    <!-- Barra de nuevo comentario -->
                    <div class="new-comment-bar">
                        <input type="text" class="new-comment-input" placeholder="Escribe un comentario"
                            v-model="newCommentText[post.id]" />
                        <button class="send-comment-btn" @click="submitComment(post)">➤</button>
                    </div>
                </div>
                <!-- FIN SECCIÓN DE COMENTARIOS -->
            </div>
            <!-- Botón "Ver más" -->
            <div v-if="hasMorePublications" class="load-more-container">
                <button @click="loadMorePublications" class="load-more-btn">
                    Ver más
                </button>
            </div>

            <!-- Mensaje cuando se acaben las publicaciones -->
            <div v-else class="no-more-container">
                <p class="no-more-text">¡Eso es todo por hoy!</p>
                <button class="reset-publications" @click="resetPublicationsPage">
                    Volver al inicio de las publicaciones
                </button>
            </div>
        </div>
    </div>

</template>

<style>
/* ==============================================
 * 1) SECCIÓN DE COMENTARIOS
 * ============================================== */

/* Contenedor principal de comentarios */
.comments-section {
    margin-top: 16px;
    /* Un poco de espacio arriba */
    background-color: #fff;
    border: 2px solid #6b5cff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Título de comentarios */
.comments-title {
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 12px;
    color: #6b5cff;
    text-align: center;
}

/* Lista de comentarios */
.comments-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 16px;
}

/* Cada comentario individual */
.comment-item {
    background: #f7f7f7;
    border-radius: 8px;
    padding: 8px;
    border: 1px solid #eee;
}

/* Encabezado del comentario */
.comment-header {
    display: flex;
    align-items: center;
    margin-bottom: 4px;
}

.comment-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1px solid #6b5cff;
    object-fit: cover;
    margin-right: 8px;
}

.comment-username {
    font-weight: bold;
    color: #333;
    margin-right: 6px;
}

.comment-time {
    font-size: 0.8rem;
    color: #666;
}

.comment-text {
    font-size: 0.9rem;
    color: #444;
}

/* Nueva entrada de comentario */
.new-comment-bar {
    display: flex;
    gap: 8px;
}

.new-comment-input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Botón de enviar nuevo comentario */
.send-comment-btn {
    background-color: #6b5cff;
    border: none;
    color: #fff;
    font-weight: bold;
    padding: 0 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

.send-comment-btn:hover {
    background-color: #4a4aff;
}

/* ==============================================
 * 2) SIDEBAR FIJO
 * ============================================== */

/* Contenedor principal del sidebar */
.my-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    /* Ancho fijo del sidebar */
    height: 100vh;
    /* Ocupa todo el alto de la ventana */
    background-color: #161B33;
    color: #F4F4F9;
    display: flex;
    flex-direction: column;
    padding: 16px;
    box-shadow: 2px 0 6px rgba(0, 0, 0, 0.2);
    z-index: 999;
    /* Asegura que quede por encima de otros elementos */
}

/* Logo o título (opcional) */
.sidebar-logo {
    margin-bottom: 24px;
}

/* Secciones dentro del sidebar */
.sidebar-section {
    margin-bottom: 16px;
}

/* Input de búsqueda (si se llega a usar) */
.sidebar-search {
    width: 100%;
    border: none;
    border-radius: 4px;
    padding: 8px;
    background-color: #3A3D62;
    color: #F4F4F9;
    font-size: 14px;
    outline: none;
}

/* Botones genéricos del sidebar */
.sidebar-btn {
    width: 100%;
    display: inline-block;
    text-align: center;
    padding: 10px;
    border: none;
    border-radius: 4px;
    color: #F4F4F9;
    font-weight: 600;
    cursor: pointer;
    background-color: #3A3D62;
    transition: background-color 0.2s ease-in-out;
}

/* Botón "Publicar" */
.publish-btn {
    background-color: #6B5CFF;
}

.publish-btn:hover {
    background-color: #4A4AFF;
}

/* Botón para perfil (si se desea efecto hover distinto) */
.profile-btn:hover {
    background-color: #4A4AFF;
}

/* Botón Logout (rojo) */
.logout-btn {
    background-color: #E53E3E;
}

.logout-btn:hover {
    background-color: #C53030;
}

/* Empuja el contenido restante hacia abajo,
   de modo que el logout quede en la parte inferior */
.sidebar-spacer {
    flex: 1;
}

/* Contenedor del botón de logout en la parte de abajo */
.sidebar-logout {
    margin-top: auto;
}

/* ==============================================
 * 3) LAYOUT Y CONTENEDORES
 * ============================================== */

/* Asegura altura y scroll interno */
.flex-grow {
    max-height: calc(80vh - 4rem);
    /* Ajusta de acuerdo con el header y footer */
    overflow-y: auto;
}

/* Contenedor principal de publicaciones (ajusta con el sidebar) */
.publications-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px;
    padding-top: 100px;
    margin-left: 250px;
    /* Deja espacio para el sidebar */
    justify-content: center;
    align-items: center;
}

/* Columna de Posts */
.posts-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ==============================================
 * 4) TARJETAS DE PUBLICACIONES (POST)
 * ============================================== */

/* Tarjeta que envuelve cada publicación */
.post-card {
    border: 2px solid #6b5cff;
    border-radius: 12px;
    padding: 20px;
    max-width: 450px;
    width: 100%;
    background-color: #fff;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.post-card:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

/* Encabezado del post (usuario, avatar, fecha) */
.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

/* Avatar del usuario */
.avatar img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid #6b5cff;
    object-fit: cover;
}

/* Información del usuario (nombre, etc.) */
.user-info {
    margin-left: 10px;
}

/* Nombre de usuario */
.username {
    font-size: 1rem;
    font-weight: bold;
    color: #333;
}

/* Fecha/hora de la publicación */
.post-time {
    font-size: 0.8rem;
    color: #888;
}

/* ==============================================
 * 5) CONTENIDO DE LA PUBLICACIÓN (IMÁGENES)
 * ============================================== */

.post-content {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    /* Fija el tamaño del área de contenido */
    overflow: hidden;
    margin: 15px 0;
}

/* Imagen única (meme-image) */
.meme-image {
    width: 100%;
    border-radius: 8px;
    border: 1px solid #ddd;
}

/* ==============================================
 * 6) ACCIONES DEL POST (LIKE, SHARE, ETC.)
 * ============================================== */

.post-actions {
    display: flex;
    justify-content: space-around;
    margin: 15px 0;
}

/* Grupo de acciones (like/comment/share) */
.action-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

/* Contador (likes, comments, shares) */
.counter {
    font-size: 0.9rem;
    font-weight: bold;
    color: #6b5cff;
}

/* Botones de acción */
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

/* ==============================================
 * 7) CAPTION Y HASHTAGS DEL POST
 * ============================================== */

.post-caption {
    text-align: center;
    margin: 15px 0;
}

/* Texto del título y descripción */
.caption-text {
    font-size: 1rem;
    font-weight: 400;
    color: #333;
}

/* Etiquetas (hashtags) */
.tags span {
    margin-right: 8px;
    color: #6b5cff;
    font-weight: bold;
    font-size: 0.9rem;
}

/* Sección de comentarios dentro del post (solo para estilo) */
.post-comments {
    text-align: center;
    font-size: 0.9rem;
    color: #6b5cff;
    margin-top: 10px;
}

/* ==============================================
 * 8) CARRUSEL DE IMÁGENES
 * ============================================== */

/* Contenedor principal del carrusel */
.carousel {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    width: 100%;
    height: 300px;
    /* Fija la altura del carrusel */
    margin-bottom: 15px;
}

/* Imagen dentro del carrusel */
.carousel-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
    /* Asegura que la imagen se adapte sin deformarse */
    border-radius: 8px;
    border: 1px solid #ddd;
}

/* Controles (botones) del carrusel */
.carousel-controls {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    transform: translateY(-50%);
    padding: 0 10px;
}

/* Botón para pasar a la imagen anterior/siguiente */
.carousel-btn {
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    /* Tamaño reducido */
    height: 24px;
    /* Tamaño reducido */
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    /* Tamaño reducido */
    transition: background-color 0.3s ease;
}

.carousel-btn:hover {
    background-color: rgba(0, 0, 0, 0.8);
}

/* ==============================================
 * 9) BOTÓN DE "VER MÁS" Y "NO HAY MÁS"
 * ============================================== */

.load-more-container {
    text-align: center;
    margin-top: 20px;
}

.load-more-btn {
    background-color: #6b5cff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.load-more-btn:hover {
    background-color: #4a4aff;
}

.no-more-container {
    text-align: center;
    margin-top: 20px;
}

.no-more-text {
    color: #888;
    font-size: 1rem;
    font-weight: bold;
}



/* ==============================================
 * 9) TRANSICIONES (FADE)
 * ============================================== */

/* Clases utilizadas en la transición (transition name="fade") */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/** cargar mas comentarios */

.no-more-comments {
    text-align: center;
    color: #888;
    font-size: 14px;
    margin-top: 10px;
}

.load-more-comments {
    background-color: #6b5cff;
    /* Púrpura principal */
    color: #fff;
    /* Texto blanco */
    font-size: 14px;
    /* Tamaño de texto más pequeño */
    font-weight: bold;
    padding: 8px 16px;
    /* Espaciado interno más pequeño */
    border: none;
    border-radius: 6px;
    /* Bordes ligeramente redondeados */
    cursor: pointer;
    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
    /* Sombra ligera */
    transition: all 0.3s ease-in-out;
    margin: 8px auto;
    /* Centrado automático */
    display: block;
    text-align: center;
}

.load-more-comments:hover {
    background-color: #6b5cff;
    /* Púrpura más oscuro */
    transform: scale(1.05);
    /* Efecto de zoom al pasar el mouse */
    box-shadow: 0 5px 8px rgba(0, 0, 0, 0.2);
}

.load-more-comments:active {
    transform: scale(0.80);
    /* Reducir ligeramente al hacer clic */
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
}

.reset-comments {
    background-color: #6f42c1;
    /* Púrpura de la paleta */
    color: #fff;
    font-size: 12px;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 8px auto;
    display: block;
    text-align: center;
    transition: all 0.3s ease;
}

.reset-comments:hover {
    background-color: #5a34a5;
    /* Más oscuro */
    transform: scale(1.05);
}

.reset-comments:active {
    transform: scale(0.95);
}

/** Reseteo de paginas de publicaciones  */
.reset-publications {
    background-color: #6f42c1;
    /* Púrpura */
    color: #fff;
    font-size: 14px;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s ease;
}

.reset-publications:hover {
    background-color: #5a34a5;
    /* Más oscuro */
    transform: scale(1.05);
}

.reset-publications:active {
    transform: scale(0.95);
}
</style>
