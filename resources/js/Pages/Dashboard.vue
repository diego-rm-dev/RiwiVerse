<template>
    <Disclosure as="nav" class="bg-[#161B33] " v-slot="{ open }">
        <nav class="
      fixed w-full z-30 
      bg-[#161B33] 
      transition-transform 
      duration-300
    " :class="{
        '-translate-y-full': !isNavbarVisible,
        'translate-y-0': isNavbarVisible
    }">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8 ml-[250px]">
                <div class="relative flex h-16 items-center justify-between">
                    <!-- Mobile menu button -->
                    <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                        <DisclosureButton
                            class="relative inline-flex items-center justify-center rounded-md p-2 text-white hover:bg-[#3A3D62] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!open" class="block size-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block size-6" aria-hidden="true" />
                        </DisclosureButton>
                    </div>

                    <!-- Navbar logo and links -->
                    <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                        <div class="hidden sm:ml-6 sm:block">
                            <div class="flex space-x-4">
                                <a v-for="item in navigation" :key="item.name" :href="item.href"
                                    :class="[item.current ? 'bg-[#6B5CFF] text-white' : 'text-[#F4F4F9] hover:bg-[#3A3D62]', 'rounded-md px-3 py-2 text-sm font-medium']"
                                    :aria-current="item.current ? 'page' : undefined">{{ item.name }}</a>
                            </div>
                        </div>
                    </div>

                    <!-- Profile, Publish, and Logout -->
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0 gap-3">


                        <!-- Notifications -->
                        <button type="button"
                            class="relative ml-3 rounded-full bg-[#161B33] p-1 text-[#F4F4F9] hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#161B33]">
                            <span class="sr-only">View notifications</span>
                            <BellIcon class="size-6" aria-hidden="true" />
                        </button>

                        <!-- Profile dropdown -->
                        <Menu as="div" class="relative ml-3">
                            <div>
                                <MenuButton
                                    class="relative flex rounded-full bg-[#161B33] text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#161B33]">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="size-8 rounded-full"
                                        src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                        alt="User Profile" />
                                    <p>{{ }}</p>
                                </MenuButton>
                            </div>
                            <transition enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                                <MenuItems
                                    class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-[#3A3D62] py-1 shadow-lg ring-1 ring-black/5 focus:outline-none">
                                    <MenuItem v-slot="{ active }">
                                    <a href="/profile"
                                        :class="[active ? 'bg-[#6B5CFF] text-white' : 'text-[#F4F4F9]', 'block px-4 py-2 text-sm']">
                                        Your Profile
                                    </a>
                                    </MenuItem>
                                    <MenuItem v-slot="{ active }">
                                    <button @click="logout"
                                        :class="[active ? 'bg-[#6B5CFF] text-white' : 'text-[#F4F4F9]', 'block px-4 py-2 text-sm']">
                                        Sign out
                                    </button>
                                    </MenuItem>
                                </MenuItems>
                            </transition>
                        </Menu>
                        <p class="text-white">{{ user.name }}</p>
                    </div>
                </div>
            </div>
        </nav>
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl h-[80vh] p-6 relative flex flex-col">
                <!-- Cerrar modal -->
                <button @click="showModal = false" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
                    ✖
                </button>

                <h2 class="text-lg font-bold mb-4">Crear Publicación</h2>

                <!-- Contenido desplazable -->
                <div class="flex-grow overflow-y-auto pr-2">
                    <form @submit.prevent="submitPost" class="grid grid-cols-2 gap-6">
                        <!-- Título -->
                        <div class="col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input v-model="post.title" id="title" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Escribe un título..." required />
                        </div>

                        <!-- Descripción -->
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

                        <!-- Subir imágenes -->
                        <div>
                            <label for="images" class="block text-sm font-medium text-gray-700">Subir imágenes</label>
                            <input id="images" type="file" multiple accept="image/*" @change="handleImageUpload"
                                class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-[#6B5CFF] file:text-white hover:file:bg-[#4A4AFF]" />
                        </div>

                        <!-- Vista previa de imágenes -->
                        <div v-if="post.images.length" class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Vista previa de imágenes:</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <img v-for="(image, index) in post.images" :key="index" :src="image.preview"
                                    class="rounded-lg shadow-md object-cover w-full h-32" />
                            </div>
                        </div>

                        <!-- Estado de la publicación -->
                        <div class="col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select v-model="post.status" id="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="published">Pública</option>
                                <option value="published">Privada</option>
                            </select>
                        </div>
                    </form>

                </div>

                <!-- Botón Publicar -->
                <div class="mt-4 flex justify-end">
                    <button type="submit" v-on:click="submitPost"
                        class="bg-[#6B5CFF] text-white px-4 py-2 rounded-md hover:bg-[#4A4AFF]">
                        Publicar
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <DisclosurePanel class="sm:hidden">
            <div class="space-y-1 px-2 pb-3 pt-2">
                <DisclosureButton v-for="item in navigation" :key="item.name" as="a" :href="item.href"
                    :class="[item.current ? 'bg-[#6B5CFF] text-white' : 'text-[#F4F4F9] hover:bg-[#3A3D62]', 'block rounded-md px-3 py-2 text-base font-medium']"
                    :aria-current="item.current ? 'page' : undefined">{{ item.name }}</DisclosureButton>
            </div>
        </DisclosurePanel>
    </Disclosure>
    <!-- Sidebar fijo -->
    <aside class="my-sidebar">
        <!-- Logo o título (opcional) -->
        <div class="sidebar-logo d-flex align-items-center justify-center">
            <div class="flex shrink-0 items-center">
                <img class="h-6 w-auto" src="../../Icons/logo-dark.svg" alt="RiwiVerse Logo" />
            </div>
        </div>

        <!-- Barra de búsqueda -->
        <div class="sidebar-section">
            <input type="text" class="sidebar-search" placeholder="Buscar..." />
        </div>

        <!-- Botón para publicar -->
        <div class="sidebar-section">
            <button @click="showModal = true" class="sidebar-btn publish-btn">
                Publicar
            </button>
        </div>

        <!-- Botón para ir al perfil -->
        <div class="sidebar-section">
            <button class="sidebar-btn profile-btn">
                Perfil
            </button>
        </div>

        <!-- Separador flexible para empujar el logout al final -->
        <div class="sidebar-spacer"></div>

        <!-- Botón Logout (parte inferior) -->
        <div class="sidebar-logout">
            <button @click="logout" class="sidebar-btn logout-btn">
                Logout
            </button>
        </div>
    </aside>

    <div class="publications-container">
        <!-- COLUMNA DE POSTS (todo en una sola columna ahora) -->
        <div class="posts-column">
            <div v-for="post in publications" :key="post.id" class="post-card">
                <!-- Header -->
                <div class="post-header">
                    <div class="avatar">
                        <img :src="post.user.avatar || 'Icons/line/user.svg'" alt="User Avatar" />
                    </div>
                    <div class="user-info">
                        <h3 class="username">{{ post.user.name }}</h3>
                        <span class="post-time">
                            {{ new Date(post.date).toLocaleString() }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div v-if="post.images.length > 1" class="post-content">
                    <!-- Carrusel para múltiples imágenes -->
                    <div class="carousel">
                        <img :src="post.images[currentImageIndex[post.id]]" alt="Post Image" class="carousel-image" />
                        <div class="carousel-controls">
                            <button @click="prevImage(post.id)" class="carousel-btn">◀</button>
                            <button @click="nextImage(post.id)" class="carousel-btn">▶</button>
                        </div>
                    </div>
                </div>
                <div v-else-if="post.images.length === 1" class="post-content">
                    <!-- Una sola imagen -->
                    <img :src="post.images[0]" alt="Post Image" class="meme-image" />
                </div>

                <!-- Actions (like, comment, share) -->
                <div class="post-actions">
                    <div class="action-group">
                        <span class="counter">{{ post.likes || 0 }}</span>
                        <button class="action-btn" aria-label="Like" @click="toggleLike(post.id)">
                            <img :src="post.isLiked ? currentIcons.like : defaultIcons.like" alt="Like" class="icon" />
                        </button>
                    </div>

                    <div class="action-group">
                        <!-- Número de comentarios -->
                        <span class="counter">{{ post.comments?.length || 0 }}</span>
                        <button class="action-btn" aria-label="Comment" @click="">
                            <img src="/Icons/line/comment.svg" alt="Comment" class="icon" />
                        </button>
                    </div>

                    <div class="action-group">
                        <span class="counter">{{ post.shares || 0 }}</span>
                        <button class="action-btn" aria-label="Share">
                            <img src="/Icons/line/share.svg" alt="Share" class="icon" />
                        </button>
                    </div>
                </div>

                <!-- Caption -->
                <div class="post-caption">
                    <p class="caption-text font-bold">{{ post.title }}</p>
                    <p class="caption-text">{{ post.text }}</p>
                    <div class="tags">
                        <span v-for="(tag, index) in post.hashtags.split(',')" :key="index">
                            #{{ tag.trim() }}
                        </span>
                    </div>
                </div>

                <!-- SECCIÓN DE COMENTARIOS (ABAJO) -->
                <div class="comments-section" v-if="post.comments">
                    <h3 class="comments-title">
                        Comentarios
                    </h3>

                    <!-- Lista de comentarios de este post -->
                    <div class="comments-list">
                        <div v-for="(comment, idx) in sortedComments(post).slice(0, 3)" :key="comment.id"
                            class="comment-item">
                            <div class="comment-header">
                                <!-- Foto y nombre del autor del comentario -->
                                <img :src="comment.user?.avatar || 'Icons/line/user.svg'" alt="avatar"
                                    class="comment-avatar" />
                                <div>
                                    <span class="comment-username">
                                        {{ comment.user?.name }}
                                    </span>
                                    <span class="comment-time">
                                        {{ new Date(comment.date).toLocaleString() }}
                                    </span>
                                </div>
                            </div>
                            <p class="comment-text">{{ comment.text }}</p>
                        </div>
                    </div>

                    <!-- Input para escribir un nuevo comentario -->
                    <div class="new-comment-bar">
                        <input type="text" class="new-comment-input" placeholder="Escribe un comentario"
                            v-model="newCommentText[post.id]" />
                        <button class="send-comment-btn" @click="submitComment(post)">
                            ➤
                        </button>
                    </div>
                </div>
                <!-- FIN SECCIÓN DE COMENTARIOS -->
            </div>
        </div>
    </div>

</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'

const isNavbarVisible = ref(true)
let lastScrollY = 0


// Controlamos qué post está seleccionado
const selectedPost = ref(null)

// Texto de un nuevo comentario
const newCommentText = ref({})

function openComments(post) {
    // Asigna el post completo para mostrar sus comentarios
    // (o solo su ID, si prefieres).
    if (selectedPost.value && selectedPost.value.id === post.id) {
        // Si ya está seleccionado, y hacen click de nuevo,
        // podrías anularlo para "cerrar". 
        // Por ahora, re-seleccionamos (o cierra):
        selectedPost.value = null
    } else {
        selectedPost.value = post
    }
}

// Para enviar un nuevo comentario
function submitComment(post) {
    if (!newCommentText.value[post.id]) {
        newCommentText.value[post.id] = ''
    }

    const text = newCommentText.value[post.id].trim()
    if (!text) return

    const formData = {
        text,
        status: 'published',
        publication_id: post.id,
        date: formatDateForMySQL(new Date().toISOString()), // formatear si MySQL requiere
    }

    router.post('/comments', formData, {
        onSuccess: () => {
            newCommentText.value[post.id] = ''
            // Opcional: agregar manualmente el comentario a post.comments
        },
        onError: (errors) => {
            console.error(errors)
        },
    })
}

// Helper para ordenar comentarios por fecha desc
function sortedComments(post) {
    // Evitamos mutar el array original con ...
    // y hacemos un sort comparando la fecha (más reciente primero).
    return [...(post.comments || [])].sort((a, b) => {
        return new Date(b.date) - new Date(a.date)
    })
}


function handleScroll() {
    const currentScrollY = window.scrollY

    // Si el usuario hace scroll hacia abajo (currentScrollY > lastScrollY), ocultamos
    // Si hace scroll hacia arriba, mostramos
    if (currentScrollY > lastScrollY) {
        isNavbarVisible.value = false
    } else {
        isNavbarVisible.value = true
    }

    // Actualizar la posición previa
    lastScrollY = currentScrollY
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
})


const currentImageIndex = ref({});


const defaultIcons = {
    like: "Icons/line/heart.svg",
};

const currentIcons = {
    like: "Icons/line/heart-filled.svg",
};

const toggleLike = (postId) => {
    const post = publications.value.find((p) => p.id === postId);
    if (post) {
        post.isLiked = !post.isLiked;
        post.likes = post.likes || 0;
        post.likes += post.isLiked ? 1 : -1;
    }
};

const { props } = usePage();
const user = props.auth.user;
const publications = computed(() => props.publications || []);

console.log("Publications data:", publications.value);

const Comments = computed(() => props.comments || []);
console.log(Comments.value);


const nextImage = (postId) => {
    const images = publications.value.find((post) => post.id === postId)?.images || [];
    if (images.length > 1) {
        currentImageIndex.value[postId] =
            (currentImageIndex.value[postId] + 1) % images.length || 0;
    }
};

// Función para retroceder al índice anterior de imagen
const prevImage = (postId) => {
    const images = publications.value.find((post) => post.id === postId)?.images || [];
    if (images.length > 1) {
        currentImageIndex.value[postId] =
            (currentImageIndex.value[postId] - 1 + images.length) % images.length || 0;
    }
};

// Inicializar índices
publications.value.forEach((post) => {
    currentImageIndex.value[post.id] = 0;
});

const showModal = ref(false);

const formatDateForMySQL = (isoDate) => {
    const date = new Date(isoDate);
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')}`;
};

const post = ref({
    title: "",
    text: "",
    hashtags: "",
    status: "published",
    images: [],
    date: formatDateForMySQL(new Date().toISOString()),
});

post.value.date = formatDateForMySQL(new Date().toISOString());

const handleImageUpload = (event) => {
    const files = event.target.files;

    Array.from(files).forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            post.value.images.push({
                file: file,
                preview: e.target.result,
            });
        };
        reader.readAsDataURL(file);
    });
};


const submitPost = () => {
    const formData = new FormData();

    // Agregar campos de texto al FormData
    formData.append("title", post.value.title);
    formData.append("text", post.value.text);
    formData.append("hashtags", post.value.hashtags);
    formData.append("status", post.value.status);
    formData.append("date", post.value.date);

    // Agregar imágenes al FormData
    post.value.images.forEach((image, index) => {
        formData.append(`images[${index}]`, image.file);
    });

    // Enviar la solicitud
    router.post("/publications", formData, {
        onSuccess: () => {
            showModal.value = false;
            post.value = {
                title: "",
                text: "",
                hashtags: "",
                status: "published",
                images: [],
                date: new Date().toISOString(),
            };
            location.reload();
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};

const navigation = [
    { name: 'Home', href: '/', current: true },
    { name: 'Training', href: '/training', current: false },
    { name: 'Projects', href: '/projects', current: false },
    { name: 'About', href: '/about', current: false },
]

// Logout function
const logout = () => {
    router.post(route('logout'))
}
</script>
<style>
/* Sección de comentarios ahora como un bloque normal */
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

.comment-item {
    background: #f7f7f7;
    border-radius: 8px;
    padding: 8px;
    border: 1px solid #eee;
}

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

/***************************
 *   SIDEBAR  (fijo)
 ***************************/

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

/***************************
 *   SECCIONES del SIDEBAR
 ***************************/

/* Contenedor genérico de secciones para espaciar un poco */
.sidebar-section {
    margin-bottom: 16px;
}

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

/***************************
 *   BOTONES del SIDEBAR
 ***************************/

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

.publish-btn {
    background-color: #6B5CFF;
}

.publish-btn:hover {
    background-color: #4A4AFF;
}

.profile-btn:hover {
    background-color: #4A4AFF;
}

.logout-btn {
    background-color: #E53E3E;
    /* rojo */
}

.logout-btn:hover {
    background-color: #C53030;
    /* rojo más oscuro */
}

/***************************
 *   ESPACIADOR
 ***************************/

/* Empuja el contenido restante hacia abajo,
   de modo que el logout quede en la parte inferior */
.sidebar-spacer {
    flex: 1;
}

/***************************
 *   ZONA LOGOUT
 ***************************/

/* Contenedor del botón de logout en la parte de abajo */
.sidebar-logout {
    margin-top: auto;
}



.flex-grow {
    max-height: calc(80vh - 4rem);
    /* Adjust for the header and footer */
    overflow-y: auto;
}

.publications-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px;
    padding-top: 100px;
    margin-left: 250px;
    justify-content: center;
    align-items: center;
    /* o lo que uses para que no tape el sidebar */
}

.posts-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}



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
    object-fit: cover;
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

.carousel-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
    /* Asegura que la imagen se adapte sin deformarse */
    border-radius: 8px;
    border: 1px solid #ddd;
}

/* Botones del carrusel */
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

/* Contenido de la publicación */
.post-content {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    /* Fija el tamaño del área de contenido */
    overflow: hidden;
}
</style>