<template>
    <Header/>
    <div class="log-reg-page">
        <form @submit.prevent="login" action="" method="post">
            <h1>Log in</h1>
            <input v-model="form.email" name="email" type="email" placeholder="Email" required>
            <input v-model="form.password" name="password" type="password" placeholder="Password" required>
            <button type="submit">Log in</button>
            <h4><a href="/registration">Create new account</a></h4>
        </form>

        <div class="show-error">
            <h4>{{form.error}}</h4>
        </div>
    </div>
</template>

<script setup>
import Header from "@/components/Header.vue";
import {onMounted, ref} from "vue";
import {useRouter} from "vue-router";
import axios from "axios";

const router = useRouter();
const getToken = async () => {
    await axios.get('/sanctum/csrf-cookie');
}
const login = async () => {
    await axios.post('/login', {
        email: form.value.email,
        password: form.value.password,
    }).then(response => {
        if (response.data.success) {
            router.push('/home');
        } else {
            form.value.error = response.data.error;
        }
    });
}

onMounted( async () => {
    await getToken();
    await axios.get('/userdata')
        .then(response => {
            if (response.data.success) {
                router.push(`/user/${response.data.user.id}`);
            }
        })
        .catch(errors => {
            console.log(errors);
        });
});

let form = ref({
    email: '',
    password: '',
    error: '',
});
</script>
