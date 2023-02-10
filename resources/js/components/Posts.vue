<template>
        <div class="post" v-bind:id="props.post_data.id">
            <div class="post-header">
                <h3><a v-bind:href="/user/ + props.post_data.user.id">{{props.post_data.user.first_name}}</a></h3>
                <p>Category: {{props.post_data.category}}</p>
                <p>{{formatDate(props.post_data.updated_at)}}</p>
            </div>
            <div class="post-middle">
                <p>{{props.post_data.text}}</p>
                <div class="post-image">
<!--                    <img src="public/images/AVATAR.png" alt="post-image">-->
                </div>
            </div>
            <div class="post-footer">
                <div v-if="props.userdata" class="post-buttons">
                    <button>Add reply</button>
                    <div v-if="props.post_data.user.id===props.userdata.id">
                        <button @click="showEditModal = true">EDIT</button>
                        <button @click="deletePost">DELETE</button>
                    </div>
                </div>
                <div class="replies-list">
                    <!--<Reply v-model:userdata="userdata" v-model:postData="postData"/>-->
                </div>
            </div>
        </div>


            <!--Edit post-->
            <div v-if="showEditModal" class="modal-change-wrap">
                <form method="post" action="" class="modal-change" @submit.prevent="editPost">
                    <div class="modal-change-header">
                        <h2>Edit post</h2>
                        <span @click="showEditModal=false;"
                              class="modal-change-close">&times;</span>
                    </div>
                    <div>
                        <h4>Select category: </h4>
                        <select v-bind:value="post_data.category" v-model="postEditData.category">
                            <option value="politics" >politics</option>
                            <option value="code">code</option>
                            <option value="food">food</option>
                        </select>
                    </div>
                    <textarea v-model="postEditData.text"></textarea>
                    <button type="submit">Edit</button>

                    <div v-if="postEditData.error">
                        <h3>{{postEditData.error}}</h3>
                    </div>
                </form>
            </div>
</template>

<script setup>
import axios from "axios";
import moment from "moment";
import {onMounted, ref} from "vue";

const formatDate = (date) => {
    return moment(String(date)).format('DD MMMM YYYY HH:mm')
}

const props = defineProps({
    post_data: {
        type: Object,
        default() {
            return {};
        }
    },
    userdata: {
        type: Object,
        default() {
            return {};
        }
    },
})

const editPost = async () => {
    await axios.post('/postEdit', {
        id: postEditData.value.id,
        category: postEditData.value.category,
        text: postEditData.value.text
    }).then(response => {
            if (response.data.success) {
                postEditData.value.error = 'Post updated';
            } else {
                postEditData.value.error = response.data.error;
            }
        })
}

const deletePost = async () => {
    if (confirm("Delete post?")) {
        await axios.get(`/postDelete/${postEditData.value.id}`);
    }
}

let showEditModal = ref(false);
let postEditData = ref({
    id: props.post_data.id,
    category: props.post_data.category,
    text: props.post_data.text,
    error: null,
});
</script>
