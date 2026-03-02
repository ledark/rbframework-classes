// @ts-nocheck
import { createApp } from '{httpSite}/assets/js/vue.esm-browser.js'

createApp({
    delimiters: ['{[', ']}'],
    data() {
        return {
            isPageReady: false,
            alertMessage: '',
            storageID: 'rbframework',
            login: '',
            senha: '',
        }
    },
    methods: {

        changeState(state) {
            switch (state) {
                case 'loading':
                    $('.login').addClass('loading');
                    $('.login').find('button > .state').html('Autenticando...');
                    break;
                case 'success':
                    $('.login').addClass('ok');
                    $('.login').find('button > .state').html('Sucesso!');
                    break;
                case 'none':
                    $('.login').removeClass('ok loading');
                    $('.login').find('button > .state').html('Entrar');
                    $('[name=login]').focus();
                    break;
            }
        },

        saveForm() {
            localStorage.setItem(this.storageID + "_login", this.login);
            localStorage.setItem(this.storageID + "_senha", this.senha);
        },

        clearForm() {
            localStorage.removeItem(this.storageID + "_login");
            localStorage.removeItem(this.storageID + "_senha");
        },

        loadForm() {
            let hasLoaded = false;
            let login = localStorage.getItem(this.storageID + "_login");
            let senha = localStorage.getItem(this.storageID + "_senha");
            if (login != null || login != undefined) {
                this.login = login;
                hasLoaded = true;
            }
            if (senha != null || senha != undefined) {
                this.senha = senha;
                hasLoaded
            }
            if (hasLoaded) {
                $('#relembrar').prop('checked', true);
            }
        },

        toogleRelembrar() {
            if ($('#relembrar').is(':checked')) {
                this.saveForm();
            } else {
                this.clearForm();
            }
        },

        doLogin() {
            this.toogleRelembrar();
            this.changeState('loading');

            axios.post('{httpSite}/login', {
                login: this.login, senha: this.senha
            }).then(response => {
                if (response.data.status == 'success') {
                    this.changeState('success');
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 1000);
                } else {
                    this.changeState('none');
                    this.alertMessage = response.data.message;
                }
            });
        },
    },
    mounted() {
        this.$nextTick(function () {
            this.isPageReady = true;
        });
        this.loadForm();
    },
}).mount('#app')