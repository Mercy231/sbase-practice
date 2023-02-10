<template>
    <div class="reply-wrap">
        <div>
            <h4>Reply to </h4>
        </div>
        <div class="reply">
            <div class="post-header">
                <h3><a href="">USERNAME</a></h3>
                <p>TIME</p>
            </div>
            <div class="post-middle">
                <p>Discover Linguapress online learning resources. All texts and worksheets are absolutely free for anyone to use. Linguapress pages can be accessed by anyone, from anywhere. No need to be on a school network, or to log in. Just google for Linguapress, and take it from there.</p>
                <div class="post-image">
                    <img src="public/images/AVATAR.png" alt="post-image">
                </div>
            </div>
            <div class="post-footer">
                <div class="post-buttons">
                    <div>
                        <button>EDIT</button>
                        <button>DELETE</button>
                    </div>
                    <button>SHOW REPLIES</button>
                </div>
                <div class="replies-list">
                    <!--                <Reply/>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Reply from "./Reply.vue";
const props = defineProps({
    userdata: {
        type: Object,
        default: () => null,
    },
    postData: {
        type: Object,
        default: () => null,
    }
})
</script>
