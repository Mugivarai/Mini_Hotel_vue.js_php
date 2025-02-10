<template>
  <div class="main-column">
    <section id="section-1">
      <div class="background-image">
        <h1 class="text-logo">ЭЛИТНЫЙ МИНИ-ОТЕЛЬ <br> ПРЯМО В ЦЕНТРЕ СЕВАСТОПОЛЯ</h1>
        <p>Забронируйте прямо сейчас и получите гарантированное заселение !</p>
        <div class="logo-hrefs">
          <a href="#">Забронировать место</a>
          <a href="/#section-2">Смотреть номера</a>
        </div>
      </div>
    </section>
    <section id="section-2">
      <div class="line-info">
        <div class="line-info-item">
          <h1>5</h1>
          <hr color="black">
          <p>филлиалов</p>
        </div>
        <div class="line-info-item">
          <h1>5</h1>
          <hr color="black">
          <p>номеров разных категорий</p>
        </div>
        <div class="line-info-item">
          <h1>15+</h1>
          <hr color="black">
          <p>партнеров</p>
        </div>
        <div class="line-info-item">
          <h1>150+</h1>
          <hr color="black">
          <p>довольных клиентов</p>
        </div>
      </div>
    </section>
    <section id="section-3">
      <div class="background-section-3">
        <h1>Номера</h1>
        <popoverRoomVue :posts="post" @create="showPopoverRoom = false" v-show="showPopoverRoom"></popoverRoomVue>
        <div class="container-slide-hotel-room">
          <div class="slide-hotel-room-bck" @click="slideRoomLeft()"></div>
          <div class="slider-hotel-room" v-bind:style="{ width: sliderWidth , transform: 'translateX(' + transformSlider + ')'}">
            <div v-for="(value,i) in roomDescripEntitys" v-bind:key=i class="slide" >
              <h1>НОМЕР {{value['name'].toUpperCase()}}</h1>
              <h1><b>ОТ {{value['price_per_day']}} &#x20bd;/СУТ</b></h1>
              <button v-bind:value="i" @click="showPopover(i)">СМОТРЕТЬ</button>
              <img :src="mainPhoto(value['photo'])" alt=""/>
            </div>
          </div>
          <div class="slide-hotel-room-nxt" @click="slideRoomRight()"></div>
        </div>
        <h1>ОБ ОТЕЛЕ</h1>
        <div class="about-hotel">
          <div class="about-hotel-text">
            <h2>MINI-HOTEL</h2>
            <p>Добро пожаловать в наш элитный отель, расположенный в самом 
              сердце Севастополя. Мы предлагаем роскошные номера, первоклассное 
              обслуживание и незабываемые впечатления. Наслаждайтесь комфортом и 
              уютом в наших стильных апартаментах, а также воспользуйтесь всеми 
              удобствами, которые мы предлагаем. Наш отель идеально подходит как 
              для деловых поездок, так и для отдыха.
            </p>
          </div>
          <div class="about-hotel-img">
            <img src="@/image/holl_section3.jpg" alt="">
          </div>
        </div>
        <div class="line-photo">
          <div v-for="(value,i) in linePhoto" v-bind:key="i" class="line-photo-item">
            <img :src="value" alt="">
          </div>
        </div>
        <h1>НАШИ ДОП. УСЛУГИ</h1>
        <div class="add-services-info-container">
          <div class="add-services-info">
            <div v-for="(value,i) in additionalServices" v-bind:key="i" class="add-services-info-row">
              <div v-if="i%2==0" class="add-services-info-column">
                <h2>{{value['name']}}</h2>
                <p>{{ value['description'] }}</p>
                <div></div>
              </div>
              <div v-if="i%2==0 && i+1<additionalServices.length" class="add-services-info-column">
                <h2>{{additionalServices[i+1]['name']}}</h2>
                <p>{{additionalServices[i+1]['description']}}</p>
                <div></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="section-4">
      <div class="background-section-4">
        <h1>ОТЗЫВЫ</h1>
        <div class="review-container">
          <div v-for="(value,i) in review" v-bind:key="i" class="review-item" >
            <h1>{{ value['fio'].toUpperCase() }}</h1>
            <h3>{{ value['star'] }}</h3>
            <p>{{ value['description'] }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
  
</template>

<script>
import popoverRoomVue from '@/components/popoverRoom.vue';

const linePhoto = [require('@/image/section-4_img1.jpg'),require('@/image/section-4_img2.jpg'),
                  require('@/image/section-4_img3.jpg'), require('@/image/section-4_img4.jpg'),
                  require('@/image/section-4_img5.jpg'), require('@/image/section-4_img6.jpg'),
                  require('@/image/section-4_img7.jpg'), require('@/image/section-4_img8.jpg')
];

export default {
  components:{
    popoverRoomVue
  },
  data(){
    return{
      roomDescripEntitys: [],
      additionalServices: [],
      review: [],
      currentSlideIndex: 0,
      post: {},
      showPopoverRoom: false,
      linePhoto: linePhoto
    }
  },
  computed: {
    sliderWidth() {
      return this.roomDescripEntitys.length * 33.333 + '%';
    },
    transformSlider() {
      if(this.currentSlideIndex!=0){
        return `${-100/this.roomDescripEntitys.length * this.currentSlideIndex}%`;
      }else{
        return `${0}%`;
      }
      
    }
  },
  created(){
    this.fetchRoomDescripEntity();
    this.fetchAdditionalServices();
    this.fetchReview();
  },
  methods:{
    async fetchRoomDescripEntity(){
      try {
        const response = await fetch('http://localhost:8081/rooms_description');

        this.roomDescripEntitys = await response.json();

        if (this.roomDescripEntitys.length > 3) {
          this.currentSlideIndex = Math.floor((this.roomDescripEntitys.length - 3) / 2);
        } else {
          this.currentSlideIndex = 0;
        }

        console.log("Массив: ",this.roomDescripEntitys);
      } catch (error) {
          console.log('Ошибка:', error);
      }
    },
    async fetchAdditionalServices(){
      try{
        const response = await fetch('http://localhost:8081/additional_services');

        this.additionalServices = await response.json();
        console.log("Массив: ",this.additionalServices);
      }
      catch(error){
        console.log('Ошибка:', error);
      }
    },
    async fetchReview(){
      try{
        const response = await fetch('http://localhost:8081/review')
        this.review = await response.json();
        console.log('Array: ',this.review);
      }catch(error){
        console.log('Ошибка:',error);
      }
    },
    slideRoomLeft() {
      const maxIndex = Math.max(this.roomDescripEntitys.length - 3, 0);
      if (this.currentSlideIndex < maxIndex) {
        this.currentSlideIndex++;
      }
    },
    slideRoomRight() {
      if (this.currentSlideIndex > 0) {
        this.currentSlideIndex--;
      }
    },
    showPopover(key){
      this.post = this.roomDescripEntitys[key];
      console.log(this.post);
      this.showPopoverRoom = true;
    },
    mainPhoto(photos){
      for (let key in photos) {
        let temp = photos[key].split('/'); 
        if (!temp[temp.length-1].includes('_')) {
          return photos[key];
        }
      }
      return photos[0] || '';
    }
  }
}

</script>

<style scoped>


.main-column{
    width: 100%;
    max-width: 1378px;
    overflow: hidden;
    box-shadow: 5px 0px 20px 0px rgba(53, 50, 50, 0.603), -5px 0px 20px 0px rgba(53, 50, 50, 0.603);
}

.background-image {
  background-image: url(@/image/back_section1.jpg);
  background-size: 100% 100%;
  background-attachment: fixed;
  width: 100%;
  min-height: 70vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.text-logo {
  font-size: 3em;
  color:white;
  text-align: center;
  font-family:'Courier New', Courier, monospace;
  text-shadow: 1px 1px  5px white;
  margin-bottom: 0%;
}

.text-logo + p {
  font-weight: bold;
  font-family:'Courier New', Courier, monospace;
  font-size: 1.3em;
  color: white;
  text-shadow: 1px 1px  5px white;
}

.logo-hrefs {
  padding-top: 10px;
  display: flex;
  justify-content: space-between;
  gap: 20px;
  align-items: center;
  margin-left: -20px;
}

.logo-hrefs a {
  font-weight: bold;
  font-family:'Courier New', Courier, monospace;
  text-decoration: none;
  color: white;
  padding: 10px;
  border: 1px solid white;
  text-shadow: 1px 1px  5px white;
  transition: all 0.5s;
}

.logo-hrefs a:hover{
  color: black;
  background-color: white;
}

.line-info {
  box-shadow: 1px 1px 10px 0px white;
  background-color: rgb(212, 218, 134);
  display: flex;
  justify-content: space-evenly;
  flex-wrap: wrap;
  flex-basis: 400px;
  padding: 40px 0px 40px 0px;
}

.line-info-item {
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-content: center;
}

.line-info-item h1 {
  text-align: center;
  font-size: 5em;
  color: black;
  margin:0%;
  font-family:Arial, Helvetica, sans-serif;
  text-shadow: 1px 1px  5px black;
}

.line-info-item p {
  text-shadow: 1px 1px  5px black;
  text-align: center;
  color: black;
  font-size: 1.3em;
  font-family:'Courier New', Courier, monospace;
}

.line-info-item hr{
  size: 1px;
  width: 70%;
}

.background-section-3 {
  background-image: url(@/image/back_section3.jpg);
  background-size: 100% 100%;
  background-attachment: fixed;
  background-position: center;
  height: auto;
  width: 100%;
  padding-top: 40px;
}

.background-section-3 > h1{
  font-family: 'Courier New', Courier, monospace;
  color: white;
  text-shadow: 1px 1px 5px white;
  font-size: 4em;
  margin: 0%;
  text-align: center;
}

.slider-hotel-room {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  min-height: 500px;
  overflow: hidden;
  transition: transform 0.5s ease-in-out; 
}

.slide {
  width: 30%;
  max-width:400px;
  height: 400px;
  background-color: white;
  border-radius: 5px;
  display: flex;
  flex-direction: column;
  align-items: center;
  overflow: hidden;
  box-shadow: 1px 1px 10px 0px white;
}

.slide img{
  width: 100%;
  height: 100%;
  margin-top: 20px;
}

.slide button {
  box-shadow: 1px 1px 10px 0px black;
  margin-top: 20px;
  background-color: black;
  color: white;
  padding: 5px 10px;
  border: 1px solid black;
  cursor: pointer;
  transition: all 0.5s;
}

.slide button:hover {
  color:black;
  background-color: white;
  border: 1px solid black;
}
.slide h1 {
  text-shadow: 1px 1px 5px black;
  margin-bottom: 0%;
  color: black;
  font-family: 'Courier New', Courier, monospace;
  font-weight: bold;
}

.container-slide-hotel-room {
  width: 100%;
  height: auto;
  position: relative;
  
}

.slide-hotel-room-nxt,
.slide-hotel-room-bck {
  position: absolute;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 1px solid white;
  z-index: 2;
  background-color: white;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: background-color 0.3s, transform 0.3s;
}

.slide-hotel-room-nxt::before {
  content: '';
  border: solid black;
  border-width: 0 3px 3px 0;
  display: inline-block;
  padding: 8px;
  transform: rotate(135deg); 
}

.slide-hotel-room-bck::after {
  content: '';
  border: solid black;
  border-width: 0 3px 3px 0;
  display: inline-block;
  padding: 8px;
  transform: rotate(-45deg); 
}

.slide-hotel-room-nxt:hover,
.slide-hotel-room-bck:hover {
  background-color: lightgray;
}

.slide-hotel-room-nxt{
  top:50%;
  transform: translateY(-50%);
}

.slide-hotel-room-bck{
  right: 0%;
  top:50%;
  transform: translateY(-50%);
}

.about-hotel{
  margin-top: 40px;
  width: 100%;
  height: auto;
  display: flex;
  justify-content: center;
  gap: 20px;
  align-items: start;
}

.about-hotel-text{
  font-family: 'Courier New', Courier, monospace;
  color: white;
  width: 40%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.about-hotel-text h2{
  text-shadow: 1px 1px 5px red;
  text-align: center;
  margin-top:0%;
}

.about-hotel-text p{
  text-shadow: 1px 1px 5px white;
  text-align: justify;
  font-size: 1.5em;
}

.about-hotel-img {
  width:40%;
  height: auto;
  max-height: 560px;
}

.about-hotel-img img{
  width: 100%;
  height: 100%;
}

.line-photo {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  margin-top:80px;
  margin-bottom: 40px;
}

.line-photo-item{
  flex-basis: 25%;
  height: 300px;
}

.line-photo-item img{
  width:100%;
  height: 100%;
}

.add-services-info-container {
  margin-top:20px;
  width: 100%;
  height: auto;
  padding-bottom:80px;
}

.add-services-info{
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  flex-direction: column;
}

.add-services-info-row{
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  width: 80%;
  height: auto;
}

.add-services-info-column {
  position:relative;
  text-align: center;
  color: white;
  width: 45%;
  height: 150px;
  font-family: 'Courier New', Courier, monospace;
}

.add-services-info-column div{
  position: absolute;
  bottom:0%;
  width: 100%;
  height: 1px;
  border: 1px solid white;
  box-shadow: 1px 1px 10px 0px white;
}

#section-4{
  width:100%;
  height: auto;
}

.background-section-4{
  background-image: url(@/image/section-5.jpg);
  box-shadow: inset 0px 0px 40px 10px black;
  background-attachment: fixed;
  background-size: 100% 100%;
  width: 100%;
  height: auto;
}

.background-section-4 > h1{
  font-family: 'Courier New', Courier, monospace;
  color: white;
  text-shadow: 1px 1px 5px white;
  font-size: 4em;
  margin: 0%;
  padding-top:20px;
  text-align: center;
}

.review-container{
  display: flex;
  justify-content: center;
  gap:40px;
  width:100%;
  height: 600px;
  align-items: center;
}

.review-item {
  height: 40%;
  width: 300px;
  background-color: rgba(0, 0, 0, 0.61);
  border-radius: 5px;
  display: flex;
  justify-content: center;
  flex-direction: column;
  text-align: center;
  font-family: 'Courier New', Courier, monospace;
  color: white;
  text-shadow: 1px 1px 5px white;
  padding: 2.5px;
}



</style>
