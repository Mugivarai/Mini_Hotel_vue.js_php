<template>
  <div class="wrapper">
    <div v-show="resultShow" class="result-modal">
        <p>{{ result }}</p>
        <button type="button" @click="resultShow=false">ЗАКРЫТЬ</button>
    </div>
    <form action="">
        <h1>LOGIN</h1>
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
            <button type="button"  @click="confirmUser">ВХОД</button>
            <button type="button" @click="resetForm">ОЧИСТИТЬ</button>
        </div>
    </form>
  </div>
</template>

<script>
export default{
    data(){
        return{
            resultShow: false,
            result: '',
            email: '',
            password: '',
            errorValidate: {
                email: '',
                password: ''
            }
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
            if(this.password==''){
                this.errorValidate.password = 'Незаполненное поле';
            }

            for(let v in this.errorValidate){
                  if(this.errorValidate[v] != ''){
                      return;
                  }
            }

            const response = await fetch('http://localhost:8081/log_in.php',{
                method: 'post',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: this.email,
                    password: this.password
                })
            });

            if( response.status == 200){
                this.result = 'Вы успешно вошли в систему';
                this.resultShow = true;
            }else{
                if(response.status == 403){
                    this.result = 'Произошла ошибка: выйдите из аккаунта админестратора';
                    this.resultShow = true;
                }else{
                    this.result = 'Произошла ошибка: неверный пароль или email';
                    this.resultShow = true;
                }
            }

        },
        resetForm(){
            this.email='';
            this.password='';

            for(let v in this.errorValidate){
                this.errorValidate[v] = '';
            }

            this.$refs.refEmail.style.boxShadow = '';
            this.$refs.refPassword.style.boxShadow = '';
        }
    }
}
</script>

<style scoped>
.wrapper{
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
    font-size:12px;
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
    background-color: rgb(255, 252, 252);
    color: rgb(0, 0, 0);
    border-radius: 5px;
}

.result-modal button{
    padding: 5px 10px;
    border-radius: 0%;
    border: 1px solid white;
    background-color: rgb(0, 0, 0);
    font-weight: bold;
    color: white;
    transition: all 0.5s;
    cursor: pointer;
}

.result-modal button:hover{
    color: black;
    background-color: white;
    border: 1px solid black;
}

</style>
