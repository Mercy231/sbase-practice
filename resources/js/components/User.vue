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
    <!--user-->

    <div class="user-page">
        <div v-if="!invalidUser" class="user-card">
            <div class="user-card-header">
                <h2>{{user.first_name}} {{user.last_name}}</h2>
                <hr>
                <div v-if="user.isOnline" class="profile-status">
                    <span class="profile-status-online-indicator">&bull;</span>
                    <p>Online</p>
                </div>
                <div v-else class="profile-status">
                    <span class="profile-status-offline-indicator">&bull;</span>
                    <p>Last seen {{user.last_seen}}</p>
                </div>
            </div>
            <div class="user-card-middle">
                <div class="user-card-middle-elem">
                    <label>Email:</label>
                    <p>{{user.email}}</p>
                    <button v-if="userprofile" @click="showModal.showModalEmail = true">Change</button>
                </div>
                <div class="user-card-middle-elem">
                    <label>Phone:</label>
                    <p>{{user.phone}}</p>
                    <button v-if="userprofile"  @click="showModal.showModalPhone = true">Change</button>
                </div>
            </div>
            <div class="user-card-footer">
                <button v-if="userprofile"  @click="showModal.showModalPassword = true">Change password</button>
                <button v-if="userprofile"  @click="logout">Log out</button>
            </div>
        </div>
        <div v-else>
            <h1>INVALID USER</h1>
        </div>
        <!--Change-email-->
        <div v-if="showModal.showModalEmail" class="modal-change-wrap">
            <form method="post" action="" class="modal-change" @submit.prevent="changeEmail">
            <div class="modal-change-header">
                <h2>Change email</h2>
                <span @click="
                showModal.showModalEmail=false;
                update.showMassage=false;
                update.email=null;
                " class="modal-change-close">&times;</span>
            </div>
                <input v-model="update.email" type="email" placeholder="New email">
                <button type="submit">Change email</button>
                <div v-if="update.showMassage">
                    <h3>{{update.message}}</h3>
                </div>
            </form>
        </div>
        <!--Change-phone-->
        <div v-if="showModal.showModalPhone" class="modal-change-wrap">
            <form method="post" action="" class="modal-change" @submit.prevent="changePhone">
            <div class="modal-change-header">
                <h2>Change phone</h2>
                <span @click="
                showModal.showModalPhone=false;
                update.showMassage=false;
                update.phone=null;
                " class="modal-change-close">&times;</span>
            </div>
                <input v-model="update.phone" type="tel" placeholder="New phone">
                <button type="submit">Change phone</button>
                <div v-if="update.showMassage">
                    <h3>{{update.message}}</h3>
                </div>
            </form>
        </div>
        <!--Change-password-->
        <div v-if="showModal.showModalPassword" class="modal-change-wrap">
            <form method="post" action="" class="modal-change" @submit.prevent="changePassword">
            <div class="modal-change-header">
                <h2>Change password</h2>
                <span @click="
                showModal.showModalPassword=false;
                update.showMassage=false;
                update.password=null;
                update.newPassword=null;
                update.newPassword_confirmation=null;
                " class="modal-change-close">&times;</span>
            </div>
                <input v-model="update.newPassword" type="password" placeholder="New password">
                <input v-model="update.newPassword_confirmation" type="password" placeholder="Confirm new password">
                <button type="submit">Change password</button>
                <div v-if="update.showMassage">
                    <h3>{{update.message}}</h3>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import axios from "axios";
import {useRoute, useRouter} from "vue-router";

let showModal = ref({
    showModalEmail: null,
    showModalPhone: null,
    showModalPassword: null,
});
let update = ref({
    email: null,
    phone: null,
    newPassword: null,
    newPassword_confirmation: null,
    showMassage: false,
    message: null,
});
let invalidUser = ref(false);
let user = ref({
    id: null,
    first_name: null,
    last_name: null,
    email: null,
    phone: null,
    isOnline: false,
    last_seen: null,
});


let userprofile = ref();
let userdata = ref();

const router = useRouter();
const route = useRoute();

const getToken = async () => {
    await axios.get('/sanctum/csrf-cookie');
}

const getUser = async () => {
    await axios.get(`/getUserdataById/${route.params.id}`)
        .then(response => {
            if (response.data.error == null) {
                user.value.id = response.data.userdata['id'];
                user.value.first_name = response.data.userdata['first_name'];
                user.value.last_name = response.data.userdata['last_name'];
                user.value.email = response.data.userdata['email'];
                user.value.phone = response.data.userdata['phone'];
                user.value.isOnline = response.data.userdata['isOnline'];
                user.value.last_seen = response.data.userdata['last_seen'];
                console.log(response.data);
            } else {
                invalidUser.value = true;
            }
        });
}
const changeEmail = async () => {
    await axios.post(`/changeEmail`, {
        id: route.params.id,
        email: update.value.email,
    })
        .then(response => {
            if (response.data.error == null) {
                update.value.message = 'Email updated successfully';
                update.value.showMassage = true;
            } else {
                update.value.message = response.data.error;
                update.value.showMassage = true;
            }
        });
}
const changePhone = async () => {
    await axios.post(`/changePhone`, {
        id: route.params.id,
        phone: update.value.phone,
    })
        .then(response => {
            if (response.data.error == null) {
                update.value.message = 'Phone updated successfully';
                update.value.showMassage = true;
            } else {
                update.value.message = response.data.error;
                update.value.showMassage = true;
            }
        });
}
const changePassword = async () => {
    await axios.post(`/changePassword`, {
        id: route.params.id,
        newPassword: update.value.newPassword,
        newPassword_confirmation: update.value.newPassword_confirmation,
    })
        .then(response => {
            if (response.data.error == null) {
                update.value.message = 'Password updated successfully';
                update.value.showMassage = true;
                update.value.newPassword = null;
                update.value.newPassword_confirmation = null;
            } else {
                console.log(response.data.userdata)
                update.value.message = response.data.error;
                update.value.showMassage = true;
            }
        });
}
const logout = async () => {
    await axios.get('/logout')
        .then(response => {
            router.push('/home');
        })
        .catch(errors => {
            console.log(errors);
        })
}
onMounted(async () => {
    await getToken();
    await getUser();
    await axios.get('/userdata')
        .then(response => {
            userdata.value = response.data.user;
        })
        .catch(errors => {
            console.log(errors);
        });
    if (userdata.value.id == user.value.id) {
        userprofile.value = true;
    } else {
        userprofile.value = false;
        window.setInterval(async () => {
            await getUser();
        },10 * 1000);
    }
});
</script>
