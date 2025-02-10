<template>
  <div class="container">
    <div class="close-modal" @click="closeModal"></div>
    <div class="modal-container">
        <div class="head">
            <h1 class="line">{{posts['name']}}</h1>
        </div>
        <div class="info">
            <div class="info-container">
                <img class="info-img" :src="mainPhoto(posts.photo)" alt="">
                <p>
                    {{posts['description']}}
                </p>
            </div>
        </div>
        <div>
            <h1 class="cost">ОТ {{posts['price_per_day']}} &#x20bd;/СУТ</h1>
        </div>
        <div class="line-photo">
            <div v-for="(value,i) in filteredPhotos" v-bind:key="i" :style="{width: imgWidth}">
                <img :src="value" alt="">
            </div>
        </div>
        
    </div>
  </div>
</template>

<script>
export default {
    props:{
        posts:{
            type: Object,
            required: true
        }
    },
    computed:{
        imgWidth(){
            if(!this.posts.photo){
                return '25%';
            }
            return 100/(this.posts.photo.length-1)+'%';
        },
        filteredPhotos() {
            if(!this.posts.photo){
                return [];
            }
            return this.posts.photo.filter(value => this.notMain(value));
        }
    },
    methods:{
        closeModal(){
            this.$emit('create',true);
        },
        mainPhoto(photos){
            if (!photos) {
                return '';
            }
            for (let key of photos) {
                let temp = key.split('/'); 
                if (!temp[temp.length-1].includes('_')) {
                    return key;
                }
            }
            return photos[0] || '';
        },
        notMain(str){
            if (!str) {
                return false;
            }
            let temp = str.split('/'); 
            if (temp[temp.length-1].includes('_')) {
                return true;
            }

            return false;
        }
    }
}
</script>

<style scoped>
.container{
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.753);
    position: fixed;
    top: 0%;
    z-index: 10;
}
.modal-container{
    box-shadow: 1px 1px 10px 0px white;
    z-index: 10;
    background-image: url(@/image/popover.jpg);
    background-position: center;
    background-size: 100% 100%;
    background-attachment: fixed;
    width:90%;
    height:90%;
    position: fixed;
    top: 5%;
    left: 5%;
    overflow-y: scroll;
    overscroll-behavior: contain;
    border-radius: 5px;
}
.head{
    box-shadow: 1px -1px 10px 0px black;
    margin-bottom: 40px;
}
.line {
    font-family: 'Courier New', Courier, monospace;
    color: white;
    text-shadow: 1px 1px 5px white;
    font-size: 3.5em;
    margin:0%;
    padding: 10px 0px 10px 0px;
    background-color: black;
    text-align: center;
}

.cost {
    font-family: 'Courier New', Courier, monospace;
    color: white;
    text-shadow: 1px 1px 5px white;
    font-size: 3em;
    margin:0%;
    padding: 10px 0px 10px 0px;
    box-shadow: 1px 1px 10px 0px black;
    background-color: black;
    text-align: center;
}

.close-modal {
    position: fixed;
    top: 2.5%;
    left: 5%;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    z-index: 11;
    transform: translateX(-50%);
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: all 0.5s;
}

.close-modal::before,
.close-modal::after {
    content: '';
    position: absolute;
    width: 16px; 
    height: 2px; 
    background-color: white; 
    border-radius: 1px; 
}

.close-modal::before {
    transform: rotate(45deg);
}

.close-modal::after {
    transform: rotate(-45deg); 
}

.close-modal:hover{
    background-color: rgba(255, 255, 255, 0.733);
}

.info{
    width: 100%;
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
}

.info-container {
    width: 90%;
    height: auto;
}

.info-img{
    width: 60%;
    height: 500px;
    float: left;
    margin-right: 20px;
    margin-bottom: 5px;
}

.info p{
    margin: 0%;
    font-family: 'Courier New', Courier, monospace;
    color: rgb(0, 0, 0);
    font-size: 1.3em;
    font-weight: bold;
    text-align: justify;
    text-shadow: 1px 1px 5px rgb(0, 0, 0);
}

.line-photo{
    display: flex;
    justify-content: center;
    gap: 0%;
    height: 300px;
}

.line-photo img{
    width: 100%;
    height: 100%;
}
</style>