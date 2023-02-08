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

    <h1>Home</h1>
</template>

<script setup>
import axios from "axios";
import {onMounted, ref} from "vue";
import {useRouter} from "vue-router";

let userdata = ref();


const router = useRouter();
const getToken = async () => {
    await axios.get('/sanctum/csrf-cookie');
}
onMounted(async () => {
    await getToken();
    await axios.get('/userdata')
        .then(response => {
            if (response.data.success) {
                console.log(response.data.user);
                userdata.value = response.data.user;
            }
        })
        .catch(errors => {
            console.log(errors);
        });
});
</script>
