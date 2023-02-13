<template>
    <div class="header">
        <div class="nav-bar">
            <h1><a href="/home">Home</a></h1>
        </div>

        <div class="user-bar">
            <div v-if="props.userdata">
                <h1><a :href="'/user/'+props.userdata.id">{{props.userdata.first_name}}</a></h1>
            </div>

            <div v-else>
                <h1><a href="/login">Login</a> | <a href="/registration">Registration</a></h1>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    userdata: {
        type: Object,
        default() {
            return null;
        }
    },
})
</script>
