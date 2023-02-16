<template>
    <div class="post" :id="props.post_data.id">
        <div class="post-header">
            <h3><a :href="'/user/'+props.post_data.user.id">{{props.post_data.user.first_name}}</a></h3>
            <p>Category: {{props.post_data.category}}</p>
            <p>{{formatDate(props.post_data.updated_at)}}</p>
        </div>

        <div class="post-middle">
            <p class="main-text">{{props.post_data.text}}</p>

            <div v-if="props.post_data.image" class="post-image">
                <img :src="'/public/storage/'+props.post_data.image" alt="post_image">
            </div>
        </div>

        <div class="post-footer">
            <div v-if="props.userdata" class="post-buttons">
                <button @click="showCommentModal = true">Add comment</button>

                <div v-if="props.post_data.user.id===props.userdata.id">
                    <button @click="showEditModal = true">EDIT</button>
                    <button @click="deletePost">DELETE</button>
                </div>
            </div>

            <div v-if="props.post_data.comment" class="replies-list">
                <Comments
                    :userdata="props.userdata"
                    :commentData="comment"
                    v-for="comment in props.post_data.comment"
                />
            </div>
        </div>
    </div>
    <!--Create-comment-->
    <div v-if="showCommentModal" class="modal-change-wrap">
        <form method="post" action="" class="modal-change" @submit.prevent="createComment">
            <div class="modal-change-header">
                <h2>Create comment</h2>
                <span @click="showCommentModal=false;" class="modal-change-close">&times;</span>
            </div>

            <input @change="handleOnchangeComment" name="image" type="file">
            <textarea v-model="createCommentData.text"></textarea>
            <button type="submit">Create</button>

            <div v-if="createCommentData.error">
                <h3>{{createCommentData.error}}</h3>
            </div>
        </form>
    </div>
    <!--Edit post-->
    <div v-if="showEditModal" class="modal-change-wrap">
        <form method="post" action="" class="modal-change" @submit.prevent="editPost">
            <div class="modal-change-header">
                <h2>Edit post</h2>
                <span @click="showEditModal=false" class="modal-change-close">&times;</span>
            </div>

            <div>
                <h4>Select category: </h4>
                <select :value="props.post_data.category.value" v-model="postEditData.category">
                    <option value="politics" >politics</option>
                    <option value="code">code</option>
                    <option value="food">food</option>
                </select>
                <input @change="handleOnchangeEdit" name="image" type="file">
            </div>

            <textarea v-model="postEditData.text">{{props.post_data.text}}</textarea>
            <button type="submit">Edit</button>

            <div v-if="postEditData.error">
                <h3>{{postEditData.error}}</h3>
            </div>
        </form>
    </div>
</template>

<script setup>
import Comments from "@/components/Comments.vue";
import axios from "axios";
import moment from "moment";
import {ref} from "vue";

const props = defineProps({
    post_data: {
        type: Object,
        default() {
            return false;
        }
    },
    userdata: {
        type: Object,
        default() {
            return false;
        }
    }
})
const createComment = async () => {
    await axios.post('/createComment', {
        post_id: props.post_data.id,
        text: createCommentData.value.text,
        image: createCommentData.value.image
    }, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }).then(response => {
        showCommentModal.value = false;
        console.log(response.data);
    })
}
const handleOnchangeComment = (e) => {
    createCommentData.value.image = e.target.files[0];
}
const editPost = async () => {
    await axios.post('/postEdit', {
        id: postEditData.value.id,
        category: postEditData.value.category,
        text: postEditData.value.text,
        image: postEditData.value.image
    }, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }).then(response => {
        if (response.data.success) {
            postEditData.value.error = 'Post updated';
            showEditModal.value = false;
        } else {
            postEditData.value.error = response.data.error;
        }
    })
}
const handleOnchangeEdit = (e) => {
    postEditData.value.image = e.target.files[0];
}
const formatDate = (date) => {
    return moment(String(date)).format('DD MMMM YYYY HH:mm')
}
const deletePost = async () => {
    if (confirm("Delete post?")) {
        await axios.get(`/postDelete/${props.post_data.id}`);
    }
}

let showEditModal = ref(false);
let showCommentModal = ref(false);
let postEditData = ref({
    id: props.post_data.id,
    category: props.post_data.category,
    text: props.post_data.text,
    image: null,
    error: null,
});
let createCommentData = ref({
    post_id: props.post_data.id,
    text: null,
    image: null,
    error: null,
});
</script>
