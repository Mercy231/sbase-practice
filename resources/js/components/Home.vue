<template>
    <div class="header">
        <div class="nav-bar">
            <h1><a href="/home">Home</a></h1>
        </div>
        <div class="user-bar">
            <div v-if="userdata">
                <h1><a v-bind:href="'/user/' + userdata.id">{{userdata.first_name}}</a></h1>
            </div>
            <div v-else>
                <h1><a href="/login">Login</a></h1>
            </div>
        </div>
    </div>
    <div class="home-page">
        <div v-if="userdata">
            <button @click="showModal.showCreateModal=true">CREATE POST</button>
        </div>
        <Posts v-bind:userdata="userdata"
               v-bind:post_data="post"
               v-for="post in posts"
        />
    </div>

    <!--Create-post-->
    <div v-if="showModal.showCreateModal" class="modal-change-wrap">
        <form method="post" action="" class="modal-change" @submit.prevent="postCreate" >
            <div class="modal-change-header">
                <h2>Create post</h2>
                <span @click="
                    showModal.showCreateModal=false;
                    postCreateData.text=null;
                    "
                    class="modal-change-close">&times;</span>
            </div>
            <div>
                <h4>Select category: </h4>
                <select v-model="postCreateData.category">
                    <option value="politics">politics</option>
                    <option value="code">code</option>
                    <option value="food">food</option>
                </select>
            </div>
            <textarea v-model="postCreateData.text" />
            <button type="submit">Create</button>

            <div v-if="postCreateData.error">
                <h3>{{postCreateData.error}}</h3>
            </div>
        </form>
    </div>
</template>

<script setup>
import axios from "axios";
import {onMounted, ref} from "vue";
import {useRoute, useRouter} from "vue-router";
import Posts from './Posts.vue';

let showModal = ref({
    showCreateModal: false,
});
let userdata = ref();
let postCreateData = ref({
    category: null,
    text: null,
    error: null,
});
let posts = ref();

const router = useRouter();
const route = useRoute();
const getToken = async () => {
    await axios.get('/sanctum/csrf-cookie');
}
const postCreate = async () => {
    await axios.post('/postCreate', {
        text: postCreateData.value.text,
        category: postCreateData.value.category
    })
    .then(response => {
        if (response.data.success) {
            showModal.value.showCreateModal = false;
            console.log('POST CREATED');
        } else {
            postCreateData.value.error = response.data.error;
        }
    });
}

const getUser = async () => {
    await axios.get('/userdata')
        .then(response => {
            if (response.data.success) {
                userdata.value = response.data.user;
            } else {
                userdata.value = null;
            }
        })
        .catch(errors => {
            console.log(errors);
        });
}

const getPosts = async () => {
    await axios.get('/getPosts')
        .then(response => {
            posts.value = response.data;
        })
}

onMounted(async () => {
    await getToken()
    await getUser()
    await getPosts()
    window.setInterval(async () => {
        await getPosts();
    },10 * 1000);
});
</script>
