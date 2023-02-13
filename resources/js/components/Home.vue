<template>
    <Header :userdata="userdata"/>

    <div class="home-page">
        <div v-if="userdata">
            <button @click="showModal.showCreateModal=true">CREATE POST</button>
        </div>

        <Posts :userdata="userdata"
               :post_data="post"
               v-for="post in posts"
        />
    </div>

    <!--Create-post-->
    <div v-if="showModal.showCreateModal" class="modal-change-wrap">
        <form method="post" action="" class="modal-change" @submit.prevent="postCreate">
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
                <input @change="handleOnchange" name="image" type="file">
            </div>

            <textarea v-model="postCreateData.text"/>
            <button type="submit">Create</button>

            <div v-if="postCreateData.error">
                <h3>{{postCreateData.error}}</h3>
            </div>
        </form>
    </div>
</template>

<script setup>
import Header from "@/components/Header.vue";
import Posts from "@/components/Posts.vue";
import axios from "axios";
import {onMounted, ref} from "vue";

const getToken = async () => {
    await axios.get('/sanctum/csrf-cookie');
}
const handleOnchange = (e) => {
    postCreateData.value.image = e.target.files[0];
}
const postCreate = async () => {
    console.log(postCreateData.value);
    await axios.post('/postCreate', {
        text: postCreateData.value.text,
        image: postCreateData.value.image,
        category: postCreateData.value.category
    }, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
        .then(async response => {
            if (response.data.success) {

                showModal.value.showCreateModal = false;
                console.log('POST CREATED');
                await getPosts()
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
            console.log(posts.value);
        })
}

onMounted(async () => {
    await getToken()
    await getUser()
    await getPosts()
    window.setInterval(async () => {
        await getPosts();
    },3 * 1000);
});

let posts = ref();
let userdata = ref();
let showModal = ref({
    showCreateModal: false,
});
let postCreateData = ref({
    category: null,
    image: null,
    text: null,
    error: null,
});
</script>
