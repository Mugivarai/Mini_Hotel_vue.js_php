<template>
    <div class="wrapper">
      <div v-show="resultShow" class="result-modal">
        <p>{{ result }}</p>
        <button type="button" @click="resultShow=false">ЗАКРЫТЬ</button>
      </div>
      <form action="">
          <h1>SIGNUP</h1>
          <div class="item">
            <p>Введите полное ФИО</p>
            <input type="text" placeholder="FIO" ref="refFio" @change="fioValidate" v-model="fio">
            <span>{{ errorValidate.fio }}</span>
          </div>
          <div class="item">
            <p>Введите номер телефона</p>
            <input type="text" placeholder="+7-978-000-00-00" v-model="phone" ref="refPhone" @change="phoneValidate">
            <span>{{ errorValidate.phone }}</span>
          </div>
          <div class="item">
              <p>Введите email</p>
              <input type="text" placeholder="Email" v-model="email" ref="refEmail" @change="emailValidate">
              <span>{{ errorValidate.email }}</span>
          </div>
          <div class="item">
              <p>Введите пароль</p>
              <input type="password" placeholder="Password" v-model="password" @change="passwordValidate" ref="refPassword">
              <span>{{ errorValidate.password }}</span>
          </div>
          <div class="item-button">
              <button type="button" @click="confirmUser">ОТПРАВИТЬ</button>
              <button type="button" @click="resetForm">ОЧИСТИТЬ</button>
          </div>
      </form>
    </div>
  </template>
  
  <script>
  export default{
      data(){
          return{
              email: '',
              password: '',
              fio: '',
              phone: '',
              errorValidate: {
                  email: '',
                  password: '',
                  fio: '',
                  phone: '',
              },
              resultShow: false,
              result: '',
          }
      },
      methods:{
          emailValidate(){
              const emailTest = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  
              if (this.email == "" || !emailTest.test(this.email)) {
                  if (this.email == "") {
                      this.errorValidate.email = "Незаполненное поле";
                  } else {
                      this.errorValidate.email = "Неккоректный email";
                  }
  
                  this.$refs.refEmail.focus();
                  this.$refs.refEmail.style.boxShadow = "0px 0px 10px 0px rgb(255,0,0)";
                  this.$refs.refEmail.addEventListener(
                      "input",
                      () => {
                          this.errorValidate.email = '';
                          this.$refs.refEmail.style.boxShadow = "";
                      },
                      { once: true }
                  );
              } else {
                  this.errorValidate.email = '';
                  this.$refs.refEmail.style.boxShadow = "0px 0px 10px 0px rgb(0,255,0)";
              }
          },
          phoneValidate(){
            const testPhone = /^\+(7|3)\d{10}$/;
            if (this.phone == "" || !testPhone.test(this.phone)) {
                if (this.phone == "") {
                    this.errorValidate.phone = "Незаполненное поле";
                } else {
                    this.errorValidate.phone = 'Неккоректный ввод';
                }
                this.$refs.refPhone.focus();
                this.$refs.refPhone.style.boxShadow = "0px 0px 10px 0px rgb(255,0,0)";
                this.$refs.refPhone.addEventListener(
                    "input",
                    () => {
                        this.errorValidate.phone = '';
                        this.$refs.refPhone.style.boxShadow = "";
                    },
                    { once: true }
                );
            } else {
                this.errorValidate.phone = '';
                this.$refs.refPhone.style.boxShadow = "0px 0px 10px 0px rgb(0,255,0)";
            }
          },
          fioValidate(){
            const fioTest = /^[А-ЯЁ][а-яё]{1,19}(?:-[А-ЯЁ][а-яё]{1,19})?\s+[А-ЯЁ][а-яё]{1,19}\s+[А-ЯЁ][а-яё]{1,19}$/;
            if (!fioTest.test(this.fio)) {
                if (this.fio == "") {
                    this.errorValidate.fio = "Незаполненное поле";
                } else {
                    this.errorValidate.fio = "Неккоректный ввод";
                }
                this.$refs.refFio.focus();
                this.$refs.refFio.style.boxShadow = "0px 0px 10px 0px rgb(255,0,0)";
                this.$refs.refFio.addEventListener(
                    "input",
                    () => {
                        this.errorValidate.fio = '';
                        this.$refs.refFio.style.boxShadow = "";
                    },
                    { once: true }
                );
            } else {
                this.errorValidate.fio = '';
                this.$refs.refFio.style.boxShadow = "0px 0px 10px 0px rgb(0,255,0)";
            }
          },
          passwordValidate(){
            if(this.password=='' || this.password.length < 3){
                if (this.password == "") {
                    this.errorValidate.password = "Незаполненное поле";
                } else {
                    this.errorValidate.password = "Пароль должен быть больше 3 симовлов";
                }
                this.$refs.refPassword.focus();
                this.$refs.refPassword.style.boxShadow = "0px 0px 10px 0px rgb(255,0,0)";
                this.$refs.refPassword.addEventListener(
                    "input",
                    () => {
                        this.errorValidate.password = '';
                        this.$refs.refPassword.style.boxShadow = "";
                    },
                    { once: true }
                );
            }else{
                this.errorValidate.password = '';
                this.$refs.refPassword.style.boxShadow = "0px 0px 10px 0px rgb(0,255,0)";
            }
          },
          async confirmUser(){
              this.emailValidate();
              this.passwordValidate();
              this.phoneValidate();
              this.fioValidate();
  
              for(let v in this.errorValidate){
                  if(this.errorValidate[v] != ''){
                    return;
                  }
              }
  
              const response = await fetch('http://localhost:8081/signup',{
                  method: 'post',
                  headers: {
                      'Accept': 'application/json',
                      'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({
                      email: this.email,
                      password: this.password,
                      fio: this.fio,
                      phone: this.phone
                  })
              });
              let temp;
            try {
                temp = await response.json(); // Парсинг JSON
            } catch (err) {
                console.error('Ошибка парсинга JSON', err);
                temp = { error: 'Неизвестная ошибка сервера' }; // На случай непредвиденного формата
            }
              console.log('temp ',temp);
              if(response.ok){
                this.result = 'Пользователь успешно добавлен';
                this.resultShow = true;
              }else{
                this.result = 'Произошла ошибка '+response.status+temp[0];
                this.resultShow = true;
              }
              
          },
          resetForm(){
            this.email='';
            this.password='';
            this.fio='';
            this.phone= '';

            for(let v in this.errorValidate){
                this.errorValidate[v] = '';
            }

            this.$refs.refEmail.style.boxShadow = '';
            this.$refs.refPassword.style.boxShadow = '';
            this.$refs.refPhone.style.boxShadow = '';
            this.$refs.refFio.style.boxShadow = '';
          }
      }
  }
  </script>
  
  <style scoped>
  .wrapper{
      flex-direction: column;
      font-family: 'Courier New', Courier, monospace;
      color: white;
      width:100%;
      height: calc(100vh - 60px);
      background-image: url('@/image/form_log2.jpg');
      background-size: 100% 100%;
      background-repeat: no-repeat;
      display: flex;
      justify-content: center;
      align-items: center;
  }
  
  form{
      padding: 10px;
      width: 300px;
      backdrop-filter: blur(10px);
      border-radius: 5px;
      display: flex;
      justify-content: center;
      flex-direction: column;
      align-items: center;
      box-shadow: 1px 1px 10px 0px white;
      gap: 20px;
  }
  
  .item{
      min-height: 75px;
      height: auto;
      width: 100%;
  }
  
  .item p{
      margin:0%;
  }
  
  form input{
      padding: 10px;
      width: 100%;
      box-sizing: border-box;
      border: 1px solid brown
  }
  
  form input::placeholder{
      font-family: 'Courier New', Courier, monospace;
  }
  
  .item-button{
      width: 100%;
      display: flex;
      justify-content: space-evenly;
  }
  
  .item-button button{
      padding: 5px 10px;
      width: 35%;
      font-family: 'Courier New', Courier, monospace;
      font-weight: bold;
      cursor: pointer;
      border-radius: 0%;
      border: 1px solid brown;
      transition: all 0.5s;
  }
  
  .item span{
        font-size: 12px;
      color: red;
  }
  
  .item-button button:hover {
      color: rgb(255, 255, 255);
      background-color: rgb(0, 0, 0);
      border: 1px solid white;
  }

.result-modal{
    padding: 5px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: fixed;
    transform: translate(-50%,-50%);
    top:50%;
    z-index:10;
    left:50%;
    width:30%;
    background-color: white;
    color: black;
    border-radius: 5px;
}
  
  </style>
  