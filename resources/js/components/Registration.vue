<template>
    <div class="header">
        <div class="nav-bar">
            <h1><a href="/home">Home</a></h1>
        </div>
        <div class="user-bar">

            <h1><a href="/login">Login</a></h1>
        </div>
    </div>
    <div class="log-reg-page">
        <form @submit.prevent="registration" action="" method="post">
            <h1>Create new account</h1>
            <input v-model="form.first_name" name="first_name" type="text" placeholder="*First name" required>
            <input v-model="form.last_name" name="last_name" type="text" placeholder="Last name">
            <input v-model="form.email" name="email" type="email" placeholder="*Email" required>
            <input v-model="form.phone" name="phone" type="tel" placeholder="Phone number">
            <input v-model="form.password" name="password" type="password" placeholder="*Password" required>
            <input v-model="form.password_confirmation" name="password_confirmation" type="password" placeholder="*Confirm password" required>
            <button type="submit">Create</button>
            <h4><a href="/login">Already have an account? Log in</a></h4>
        </form>
        <div class="show-error">
            <h4>{{form.error}}</h4>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import axios from "axios";
import {useRouter} from "vue-router";

let form = ref({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    error: '',
});
const router = useRouter();
const getToken = async () => {
    await axios.get('/sanctum/csrf-cookie');
}
const registration = async () => {
    await axios.post('/registration', {
        first_name: form.value.first_name,
        last_name: form.value.last_name,
        email: form.value.email,
        phone: form.value.phone,
        password: form.value.password,
        password_confirmation: form.value.password_confirmation,
    }).then(response => {
        if (response.data.success) {
            form.value.error = 'YOU ARE LOGGED IN!';
            router.push('/home');
        } else {
            form.value.error = response.data.error;
            console.log(response.data);
        }
    });
}
onMounted(async () => {
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
</script>
