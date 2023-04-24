<template>
  <q-layout class="fit column justify-center"
    style="background-image: url('src/assets/login_picture_small.jpg'); background-size: cover; background-position: center;">
    <div class="row justify-center">
      <q-card>
        <q-form @submit="loginProcess" class="q-gutter-md">
          <q-card-section>
            <q-img src="src\assets\undraw_login_re_4vu2.svg" />
            <h6 class="q-ma-md">Please login to your account...</h6>
            <q-input class="q-mb-md" outlined v-model="loginForm.email.value" label="Email" :rules="[ (val, rules) => rules.email(val) || 'Please enter a valid email address' ]">
              <template v-slot:prepend>
                <q-icon name="mail" />
              </template>
            </q-input>
            <q-input :type="showPassword ? 'password' : 'text'" class="q-mb-sm" outlined v-model="loginForm.password.value" label="Password" :rules="[ (val) => val.length > 0 || 'Please enter a password' ]">
              <template v-slot:prepend>
                <q-icon name="lock" />
              </template>
              <template v-slot:append>
                <q-icon
                  :name="showPassword ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="showPassword = !showPassword"
                />
              </template>
            </q-input>
            <div class="row justify-end">
              <q-btn v-if="loginProcessState" color="primary" style="width: 2cm;" disable>
                <q-spinner />
              </q-btn>
              <q-btn v-if="!loginProcessState" color="primary" style="width: 2cm;" type="submit" label="Login" />
            </div>
          </q-card-section>
        </q-form>
      </q-card>
    </div>
    <div class="text-center q-mt-md">
      <q-img src="src\assets\gofit-logo.webp" width="256px" />
    </div>
  </q-layout>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { api } from 'boot/axios'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'

export default defineComponent({
  name: 'IndexPage',
  setup() {
    const router = useRouter()
    const $q = useQuasar()
    const loginForm = {
      email: ref(''),
      password: ref('')
    }
    const loginProcessState = ref(false)
    function loginProcess() {
      loginProcessState.value = true
      api.post('/autentikasi/login', {
        EMAIL: loginForm.email.value,
        PASSWORD: loginForm.password.value
      }, {withCredentials: true})
        .then((response) => {
          if(response.data['ID_PEGAWAI']!=null) {
            $q.notify({
              message: 'Welcome back, ' + response.data.NAMA_USER + '!',
              color: 'positive',
              icon: 'check_circle',
              position: 'top-right'
            })
            sessionStorage.setItem('user', JSON.stringify(response.data))
            router.push('/panel')
          } else {
            $q.notify({
              message: response.data.NAMA_USER + ' is not an employee!',
              color: 'negative',
              icon: 'error',
              position: 'top-right'
            })
          }
          loginProcessState.value = false
        })
        .catch(() => {
          $q.notify({
            message: 'Email or password is incorrect!',
            color: 'negative',
            icon: 'error',
            position: 'top-right'
          })
          loginProcessState.value = false
        });
    }
    let showPassword = ref(true)
    return {
      loginForm,
      loginProcessState,
      loginProcess,
      showPassword
    }
  }
});
</script>
