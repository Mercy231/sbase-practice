<template>
<div class="comment-wrap">
    <svg xmlns="http://www.w3.org/2000/svg" height="40" fill="currentColor" class="bi bi-arrow-return-right" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 0 1 2v4.8a2.5 2.5 0 0 0 2.5 2.5h9.793l-3.347 3.346a.5.5 0 0 0 .708.708l4.2-4.2a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 8.3H3.5A1.5 1.5 0 0 1 2 6.8V2a.5.5 0 0 0-.5-.5z"/>
    </svg>

    <div class="comment">
        <div class="comment-header">
            <h4><a :href="'/user/'+props.commentData.user.id">{{props.commentData.user.first_name}}</a></h4>
            <p>{{formatDate(props.commentData.updated_at)}}</p>
        </div>

        <div class="comment-middle">
            <p class="main-text">{{props.commentData.text}}</p>
            <div v-if="commentData.image" class="post-image">
                <img :src="'/public/storage/'+props.commentData.image" alt="post_image">
            </div>
        </div>

        <div class="comment-footer">
            <div class="post-buttons">
                <button v-if="props.userdata" @click="showReplyModal = true">Add reply</button>
                <div v-if="props.userdata!=null && props.commentData.user.id===props.userdata.id">
                    <button @click="showReplyEdit=true">EDIT</button>
                    <button @click="deleteReply">DELETE</button>
                </div>
            </div>

            <div v-if="props.commentData.comment" class="replies-list">
                <Comments
                    :userdata="props.userdata"
                    :commentData="comment"
                    v-for="comment in props.commentData.comment"
                />
            </div>
        </div>
    </div>

</div>

<!--Create-reply-->
<div v-if="showReplyModal" class="modal-change-wrap">
    <form method="post" action="" class="modal-change" @submit.prevent="createReply">
        <div class="modal-change-header">
            <h2>Create reply</h2>
            <span @click="showReplyModal=false" class="modal-change-close">&times;</span>
        </div>
        <div>
            <input @change="handleOnchangeReply" name="image" type="file">
        </div>
        <textarea v-model="createReplyData.text"></textarea>
        <button type="submit">Reply</button>
        <div v-if="createReplyData.error">
            <h3>{{createReplyData.error}}</h3>
        </div>
    </form>
</div>

<!--Edit-reply-->
<div v-if="showReplyEdit" class="modal-change-wrap">
    <form method="post" action="" class="modal-change" @submit.prevent="editReply">
        <div class="modal-change-header">
            <h2>Edit reply</h2>
            <span @click="showReplyEdit=false" class="modal-change-close">&times;</span>
        </div>
        <div>
            <input @change="handleOnchangeEdit" name="image" type="file">
        </div>
        <textarea v-model="replyEditData.text"></textarea>
        <button type="submit">Edit</button>
        <div v-if="replyEditData.error">
            <h3>{{replyEditData.error}}</h3>
        </div>
    </form>
</div>
</template>

<script setup>
import moment from "moment/moment";
import {ref} from "vue";
import axios from "axios";

const props = defineProps({
    userdata: {
        type: Object,
        default() {
            return null;
        }
    },
    commentData: {
        type: Object,
        default() {
            return null;
        }
    }
})
const formatDate = (date) => {
    return moment(String(date)).format('DD MMMM YYYY HH:mm')
}
const handleOnchangeEdit = (e) => {
    replyEditData.value.image = e.target.files[0];
}

const handleOnchangeReply = (e) => {
    createReplyData.value.image = e.target.files[0];
}
const createReply = async () => {
    await axios.post('/createReply', {
        post_id: props.commentData.post_id,
        parent_id: props.commentData.id,
        text: createReplyData.value.text,
        image: createReplyData.value.image
    }, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }).then(response => {
        showReplyModal.value = false;
        console.log(response.data);
    })
}
const editReply = async () => {
    await axios.post('/replyEdit', {
        id: replyEditData.value.id,
        category: replyEditData.value.category,
        text: replyEditData.value.text,
        image: replyEditData.value.image
    }, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }).then(response => {
        if (response.data.success) {
            replyEditData.value.error = 'Post updated';
            showReplyEdit.value = false;
        } else {
            replyEditData.value.error = response.data.error;
        }
    })
}
const deleteReply = async () => {
    if (confirm("Delete post?")) {
        await axios.get(`/replyDelete/${props.commentData.id}`);
    }
}

let showReplyEdit = ref(false);
let showReplyModal = ref(false);
let replyEditData = ref({
    id: props.commentData.id,
    text: props.commentData.text,
    image: null,
    error: null,
});
let createReplyData = ref({
    post_id: props.commentData.id,
    text: null,
    image: null,
    error: null,
});
</script>
