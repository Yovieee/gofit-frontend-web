<template>
  <div class="col text-h3 q-mx-md q-my-sm self-center">Members</div>
  <div class="q-px-md">
    <div class="no-margin" :style="rows.length == 0 ? 'display: block' : 'display: none'">
      <q-markup-table>
        <thead>
          <tr>
            <th class="no-border" colspan="8">
              <div class="row q-my-xs">
                <q-input outlined dense v-model="searchString" label="Search here...">
                  <template v-slot:append>
                    <q-icon v-if="searchString !== ''" name="close" @click="searchString = ''" class="cursor-pointer" />
                    <q-icon name="search" />
                  </template>
                </q-input>
                <q-space />
                <q-btn flat icon="add" color="primary" label="New" @click="showNewDialog" />
              </div>
            </th>
          </tr>
          <tr>
            <th class="text-left" style="width: 0.5cm">
              ID
            </th>
            <th class="text-left" style="width: 2.5cm">
              Photo
            </th>
            <th class="text-left" style="width: 3.5cm">
              Name
            </th>
            <th class="text-left" style="width: 4.5cm">
              Email
            </th>
            <th class="text-left" style="width: 6cm">
              Date of Birth
            </th>
            <th class="text-left" style="width: 10cm">
              Address
            </th>
            <th class="text-left" style="width: 4cm">
              Phone
            </th>
            <th class="text-left">
              Action
            </th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="n in 7" :key="n">
            <td class="text-left">
              <q-skeleton animation="blink" type="text" width="20px" />
            </td>
            <td class="text-right">
              <q-skeleton animation="blink" type="circle" width="50px" />
            </td>
            <td class="text-right">
              <q-skeleton animation="blink" type="text" width="35px" />
            </td>
            <td class="text-right">
              <q-skeleton animation="blink" type="text" width="65px" />
            </td>
            <td class="text-right">
              <q-skeleton animation="blink" type="text" width="25px" />
            </td>
            <td class="text-right">
              <q-skeleton animation="blink" type="text" width="85px" />
            </td>
            <td class="text-right">
              <q-skeleton animation="blink" type="text" width="85px" />
            </td>
            <td class="text-left">
              <q-icon class="q-mr-md" name="key" color="orange" size="sm" />
              <q-icon class="q-mr-md" name="badge" color="purple" size="sm" />
              <q-icon class="q-mr-md" name="edit" color="positive" size="sm" />
              <q-icon name="delete" color="negative" size="sm" />
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td class="text-right">
              <q-icon class="q-mr-md" name="navigate_before" color="grey" size="sm" />
              <q-icon class="q-mr-xs" name="navigate_next" color="grey" size="sm" />
            </td>
          </tr>
        </tbody>
      </q-markup-table>
    </div>
    <q-table :filter="searchString" :rows="rows" :columns="columns" row-key="id" :rows-per-page-options="[7]"
      :style="rows.length == 0 ? 'display: none' : 'display: block'">
      <template v-slot:top>
        <q-input outlined dense v-model="searchString" label="Search here...">
          <template v-slot:append>
            <q-icon v-if="searchString !== ''" name="close" @click="searchString = ''" class="cursor-pointer" />
            <q-icon name="search" />
          </template>
        </q-input>
        <q-space />
        <q-btn flat icon="add" color="primary" label="New" @click="showNewDialog" />
      </template>
      <template v-slot:body-cell-photo="props">
        <q-td :props="props">
          <q-avatar>
            <q-icon v-if="props.row.FOTO_USER == null || props.row.FOTO_USER.length == 0" name="account_circle" size="xl"
              color="grey" />
            <q-img v-if="!(props.row.FOTO_USER == null || props.row.FOTO_USER.length == 0)"
              :src="'data:image/jpeg;base64,' + props.row.FOTO_USER" />
          </q-avatar>
        </q-td>
      </template>
      <template v-slot:body-cell-action="props">
        <q-td :props="props">
          <q-btn flat round color="orange" icon="key" @click="recoverPasswordConfirm(props.row)" />
          <q-btn flat round color="purple" icon="badge" @click="displayMemberCard(props.row)" />
          <q-btn flat round color="positive" icon="edit" @click="showEditDialog(props.row)" />
          <q-btn flat round color="negative" icon="delete" @click="deleteDataConfirm(props.row)" />
        </q-td>
      </template>
    </q-table>
  </div>
  <q-dialog v-model="editDialog" persistent>
    <q-card style="min-width: 15cm;">
      <q-form @submit="saveEditData">
        <q-card-section class="q-pb-none">
          <div class="text-h6">Edit Member</div>
        </q-card-section>
        <div class="row">
          <div class="col relative-position">
            <div class="absolute-center">
              <q-card-section class="q-pb-none">
                <input id="editDataPhotoInput" ref="editDataPhotoInput"
                  @input="editDataPhotoResizeCropAndConvertToBase64()" type="file" accept="image/*" hidden />
                <div class="text-center">
                  <q-avatar style="height: 5cm; width: 5cm;">
                    <q-btn round icon="add_a_photo" style="height: 5cm; width: 5cm;" color="grey"
                      @click="this.$refs.editDataPhotoInput.click()" v-if="editDataPhotoHover"
                      @mouseleave="editDataPhotoHover = false" />
                    <q-btn
                      v-if="(editData.photo.value == null || editData.photo.value.length == 0) && !editDataPhotoHover"
                      icon="account_circle" style="height: 5cm; width: 5cm;" color="grey"
                      @mouseenter="editDataPhotoHover = true" />
                    <img style="height: 5cm; width: 5cm;"
                      v-if="!(editData.photo.value == null || editData.photo.value.length == 0) && !editDataPhotoHover"
                      :src="'data:image/jpeg;base64,' + editData.photo.value" @mouseenter="editDataPhotoHover = true" />
                  </q-avatar>
                </div>
              </q-card-section>
            </div>
          </div>
          <div class="col">
            <q-card-section class="q-pb-xs">
              <q-input dense outlined v-model="editData.id.value" label="ID" disable />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-lg">
              <q-input dense outlined v-model="editData.name.value" label="Name"
                :rules="[val => !!val || 'Name is missing']" />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input dense outlined v-model="editData.email.value" label="Email"
                :rules="[val => !!val || 'Email is missing', val => /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/.test(val) || 'Invalid email']" />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input dense outlined v-model="editData.dateOfBirth.value" mask="date"
                :rules="[val => !!val || 'Date of Birth is missing', 'date']" label="Date of Birth">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                      <q-date v-model="editData.dateOfBirth.value">
                        <div class="row items-center justify-end">
                          <q-btn v-close-popup label="Close" color="primary" flat />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input dense outlined v-model="editData.address.value" label="Address"
                :rules="[val => !!val || 'Address is missing']" />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input dense outlined v-model="editData.phone.value" label="Phone"
                :rules="[val => !!val || 'Phone is missing', val => /^\d+$/.test(val) || 'Invalid phone']" />
            </q-card-section>
          </div>
        </div>
        <q-card-section>
          <div class="row justify-end">
            <q-btn class="q-mr-sm" label="Cancel" color="primary" flat @click="closeEditDialog()" />
            <q-btn type="submit" label="Save" color="primary" />
          </div>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
  <q-dialog v-model="newDialog" persistent>
    <q-card style="min-width: 15cm;">
      <q-form @submit="saveNewData">
        <q-card-section class="q-pb-none">
          <div class="text-h6">New Member</div>
        </q-card-section>
        <div class="row">
          <div class="col relative-position">
            <div class="absolute-center">
              <q-card-section class="q-pb-none">
                <input id="newDataPhotoInput" ref="newDataPhotoInput" @input="newDataPhotoResizeCropAndConvertToBase64()"
                  type="file" accept="image/*" hidden />
                <div class="text-center">
                  <q-avatar style="height: 5cm; width: 5cm;">
                    <q-btn round icon="add_a_photo" style="height: 5cm; width: 5cm;" color="grey"
                      @click="this.$refs.newDataPhotoInput.click()" v-if="newDataPhotoHover"
                      @mouseleave="newDataPhotoHover = false" />
                    <q-btn v-if="newData.photo.value == null && !newDataPhotoHover" icon="account_circle"
                      style="height: 5cm; width: 5cm;" color="grey" @mouseenter="newDataPhotoHover = true" />
                    <img style="height: 5cm; width: 5cm;" v-if="newData.photo.value != null && !newDataPhotoHover"
                      :src="'data:image/jpeg;base64,' + newData.photo.value" @mouseenter="newDataPhotoHover = true" />
                  </q-avatar>
                </div>
              </q-card-section>
            </div>
          </div>
          <div class="col">
            <q-card-section class="q-pb-xs" :style="newData.id.value.length == 0 ? 'display: block' : 'display: none'">
              <q-field label="ID" outlined dense stack-label>
                <template v-slot:control>
                  <div class="self-center full-width no-outline" tabindex="0">
                    <q-skeleton type="text" />
                  </div>
                </template>
              </q-field>
            </q-card-section>
            <q-card-section class="q-pb-xs" :style="newData.id.value.length != 0 ? 'display: block' : 'display: none'">
              <q-input dense outlined v-model="newData.id.value" label="ID" disable />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-lg">
              <q-input dense outlined v-model="newData.name.value" label="Name"
                :rules="[val => !!val || 'Name is missing']" />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input dense outlined v-model="newData.email.value" label="Email"
                :rules="[val => !!val || 'Email is missing', val => /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/.test(val) || 'Invalid email']" />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input dense outlined v-model="newData.dateOfBirth.value" mask="date"
                :rules="[val => !!val || 'Date of Birth is missing', 'date']" label="Date of Birth">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                      <q-date v-model="newData.dateOfBirth.value">
                        <div class="row items-center justify-end">
                          <q-btn v-close-popup label="Close" color="primary" flat />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input dense outlined v-model="newData.address.value" label="Address"
                :rules="[val => !!val || 'Address is missing']" />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input dense outlined v-model="newData.phone.value" label="Phone"
                :rules="[val => !!val || 'Phone is missing', val => /^\d+$/.test(val) || 'Invalid phone']" />
            </q-card-section>
          </div>
        </div>
        <q-card-section>
          <div class="row justify-end">
            <q-btn class="q-mr-sm" label="Cancel" color="primary" flat @click="closeNewDialog()" />
            <q-btn type="submit" label="Save" color="primary" />
          </div>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
  <q-dialog v-model="memberCardDialog">
    <q-card>
      <q-card-section class="q-pb-none">
        <div class="row">
          <div class="col">
            <div class="text-h6">Member Card</div>
          </div>
          <div class="col-auto q-pr-sm">
            <a v-if="!memberCardDialogSpinnerState"
              :href="memberCardOpenedCanvas.toDataURL('image/png').replace('image/png', 'image/octet-stream')"
              :download="'membercard_(' + memberCardOpenedMemberID + ').png'" style="color: black">
              <q-icon class="cursor-pointer q-mt-xs" size="sm" name="download" />
            </a>
          </div>
          <div class="col-auto q-px-sm">
            <q-icon v-if="!memberCardDialogSpinnerState" class="cursor-pointer q-mt-xs" size="sm" name="print"
              @click="printMemberCard" />
          </div>
          <div class="col-auto q-pl-sm">
            <q-icon class="cursor-pointer q-mt-xs" size="sm" name="close" @click="closeMemberCard" />
          </div>
        </div>
      </q-card-section>
      <q-card-section class="q-pt-sm">
        <div v-if="memberCardDialogSpinnerState" style="width: 428px;" class="text-center">
          <q-spinner />
        </div>
        <div id="memberCardDisplay"></div>
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-menu touch-position context-menu>
    <q-list dense>
      <q-item clickable v-close-popup @click="fetchMembers">
        <q-item-section>Refresh</q-item-section>
      </q-item>
    </q-list>
  </q-menu>
</template>

<script>
import { nanoid } from 'nanoid'
import { defineComponent, onMounted, ref } from 'vue'
import { api } from 'boot/axios'
import { useQuasar } from 'quasar'
export default defineComponent({
  name: 'MembersComponent',
  setup() {
    const $q = useQuasar()
    const columns = [
      {
        name: 'id',
        required: false,
        label: 'ID',
        align: 'left',
        field: row => row.ID_MEMBER,
        format: val => `${val}`,
        sortable: true
      },
      {
        name: 'photo',
        required: false,
        label: 'Photo',
        align: 'left',
        sortable: true
      },
      {
        name: 'name',
        required: false,
        label: 'Name',
        align: 'left',
        field: row => row.NAMA_USER,
        format: val => `${val}`,
        sortable: true
      },
      {
        name: 'email',
        required: false,
        label: 'Email',
        align: 'left',
        field: row => row.EMAIL_USER,
        format: val => `${val}`,
        sortable: true
      },
      {
        name: 'dateOfBirth',
        required: false,
        label: 'Date of Birth',
        align: 'left',
        field: row => row.TANGGAL_LAHIR_USER,
        format: val => `${['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][new Date(val).getDay()] + ', ' + new Date(val).getDate() + ' ' + new Date(val).toLocaleString('default', { month: 'long' }) + ' ' + new Date(val).getFullYear()}`,
        sortable: true
      },
      {
        name: 'address',
        required: false,
        label: 'Address',
        align: 'left',
        field: row => row.ALAMAT_MEMBER,
        format: val => `${val}`,
        sortable: true
      },
      {
        name: 'phone',
        required: false,
        label: 'Phone',
        align: 'left',
        field: row => row.TELEPON_MEMBER,
        format: val => `${val}`,
        sortable: true
      },
      {
        name: 'action',
        required: false,
        label: 'Action',
        align: 'left'
      }
    ]
    const rows = ref([])
    const editDialog = ref(false)
    const editData = {
      id_user: ref(''),
      id: ref(''),
      photo: ref(''),
      name: ref(''),
      email: ref(''),
      dateOfBirth: ref(''),
      address: ref(''),
      phone: ref('')
    }
    const editDataPhotoHover = ref(false)
    const newDialog = ref(false)
    const newData = {
      id_user: ref(''),
      id: ref(''),
      photo: ref(null),
      name: ref(''),
      email: ref(''),
      dateOfBirth: ref(''),
      address: ref(''),
      phone: ref('')
    }
    const newDataPhotoHover = ref(false)
    const searchString = ref('')
    const memberCardDialog = ref(false)
    const memberCardDialogSpinnerState = ref(true)
    const memberCardOpenedCanvas = ref(null)
    const memberCardOpenedMemberID = ref('')
    function fetchMembers() {
      rows.value = []
      api.get('/member', {
        withCredentials: true
      })
        .then(response => {
          rows.value = response.data
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while fetching users',
            color: 'negative',
            position: 'top-right'
          })
        })
    }
    function generateID() {
      api.get('/member/generateID',
        {
          withCredentials: true
        })
        .then(response => {
          newData.id.value = response.data[0]
          newData.id_user.value = nanoid(32)
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while generating ID',
            color: 'negative',
            position: 'top-right'
          })
          newDialog.value = false
        })
    }
    function showEditDialog(user) {
      editDialog.value = true
      editData.id_user.value = user.ID_USER
      editData.id.value = user.ID_MEMBER
      editData.photo.value = user.FOTO_USER
      editData.name.value = user.NAMA_USER
      editData.email.value = user.EMAIL_USER
      editData.dateOfBirth.value = user.TANGGAL_LAHIR_USER.substring(0, 10).replace('-', '/').replace('-', '/')
      editData.address.value = user.ALAMAT_MEMBER
      editData.phone.value = user.TELEPON_MEMBER
    }
    function editDataPhotoResizeCropAndConvertToBase64() {
      var fileInput = document.getElementById('editDataPhotoInput')
      var file = fileInput.files[0]
      var reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = function (event) {
        var img = new Image()
        img.src = event.target.result
        img.onload = function () {
          var canvas = document.createElement('canvas')
          var ctx = canvas.getContext('2d')
          var width = 256
          var height = 256
          var offsetX = 0
          var offsetY = 0
          if (img.width > img.height) {
            height = Math.round(img.height * (width / img.width))
            offsetY = Math.round((256 - height) / 2)
          } else {
            width = Math.round(img.width * (height / img.height))
            offsetX = Math.round((256 - width) / 2)
          }
          canvas.width = 256
          canvas.height = 256
          ctx.fillStyle = '#ffffff'
          ctx.fillRect(0, 0, 256, 256)
          ctx.drawImage(img, offsetX, offsetY, width, height)
          var base64 = canvas.toDataURL('image/jpeg')
          editData.photo.value = base64.substring(23)
        }
      }
    }
    function closeEditDialog() {
      editDialog.value = false
      editData.id_user.value = ''
      editData.id.value = ''
      editData.photo.value = null
      editData.name.value = ''
      editData.email.value = ''
      editData.dateOfBirth.value = ''
      editData.address.value = ''
      editData.phone.value = ''
    }

    function showNewDialog() {
      newDialog.value = true
      generateID()
    }
    function newDataPhotoResizeCropAndConvertToBase64() {
      var fileInput = document.getElementById('newDataPhotoInput')
      var file = fileInput.files[0]
      var reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = function (event) {
        var img = new Image()
        img.src = event.target.result
        img.onload = function () {
          var canvas = document.createElement('canvas')
          var ctx = canvas.getContext('2d')
          var width = 256
          var height = 256
          var offsetX = 0
          var offsetY = 0
          if (img.width > img.height) {
            height = Math.round(img.height * (width / img.width))
            offsetY = Math.round((256 - height) / 2)
          } else {
            width = Math.round(img.width * (height / img.height))
            offsetX = Math.round((256 - width) / 2)
          }
          canvas.width = 256
          canvas.height = 256
          ctx.fillStyle = '#ffffff'
          ctx.fillRect(0, 0, 256, 256)
          ctx.drawImage(img, offsetX, offsetY, width, height)
          var base64 = canvas.toDataURL('image/jpeg')
          newData.photo.value = base64.substring(23)
        }
      }
    }
    function closeNewDialog() {
      newDialog.value = false
      newData.id_user.value = ''
      newData.id.value = ''
      newData.photo.value = null
      newData.name.value = ''
      newData.email.value = ''
      newData.dateOfBirth.value = ''
      newData.address.value = ''
      newData.phone.value = ''
    }
    function deleteData(id_member, id_user) {
      api.post('/member/delete', {
        ID_MEMBER: id_member,
        ID_USER: id_user
      }, { withCredentials: true })
        .then(() => {
          $q.notify({
            message: 'Success deleting user',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right'
          })
          fetchMembers()
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while deleting user',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right'
          })
        })
    }
    function deleteDataConfirm(user) {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure you want to delete ' + user.NAMA_USER + '? This action cannot be undone.',
        cancel: true,
        persistent: true,
        ok: {
          color: 'negative',
          label: 'Delete'
        }
      }).onOk(() => {
        deleteData(user.ID_MEMBER, user.ID_USER)
      })
    }
    function saveEditData() {
      api.post('/member/update', {
        ID_USER: editData.id_user.value,
        ID_MEMBER: editData.id.value,
        NAMA_USER: editData.name.value,
        EMAIL_USER: editData.email.value,
        TANGGAL_LAHIR_USER: editData.dateOfBirth.value,
        ALAMAT_MEMBER: editData.address.value,
        TELEPON_MEMBER: editData.phone.value,
        FOTO_USER: editData.photo.value
      }, { withCredentials: true })
        .then(() => {
          $q.notify({
            message: 'Success editing user',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right'
          })
          fetchMembers()
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while editing user',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right'
          })
        })
      closeEditDialog()
    }
    function saveNewData() {
      api.post('/member/create', {
        ID_USER: newData.id_user.value,
        ID_MEMBER: newData.id.value,
        NAMA_USER: newData.name.value,
        EMAIL_USER: newData.email.value,
        TANGGAL_LAHIR_USER: newData.dateOfBirth.value,
        ALAMAT_MEMBER: newData.address.value,
        TELEPON_MEMBER: newData.phone.value,
        FOTO_USER: newData.photo.value
      }, { withCredentials: true })
        .then(() => {
          $q.notify({
            message: 'Success adding user',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right'
          })
          fetchMembers()
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while adding user',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right'
          })
        })
      closeNewDialog()
    }
    function displayMemberCard(member) {
      memberCardDialog.value = true
      memberCardOpenedMemberID.value = member.ID_MEMBER
      var background = new Image()
      background.src = 'src/assets/membercard.jpg'
      setTimeout(() => {
        var canvas = document.createElement('canvas')
        canvas.width = 428
        canvas.height = 270
        document.getElementById('memberCardDisplay').appendChild(canvas)
        var ctx = canvas.getContext('2d')
        ctx.globalCompositeOperation = 'darken'
        ctx.drawImage(background, 0, 0)
        ctx.font = '50px Roboto'
        ctx.fillText((member.NAMA_USER.length > 15 ? member.NAMA_USER.substring(0, 12) + '...' : member.NAMA_USER), 25, 140)
        ctx.font = '18px Courier New'
        ctx.fillText('MEMBER ID: ' + member.ID_MEMBER, 25, 200)
        ctx.fillText('ADDRESS  : ' + (member.ALAMAT_MEMBER.length > 23 ? member.ALAMAT_MEMBER.substring(0, 21) + '...' : member.ALAMAT_MEMBER), 25, 220)
        ctx.fillText('PHONE    : ' + member.TELEPON_MEMBER, 25, 240)
        memberCardDialogSpinnerState.value = false
        memberCardOpenedCanvas.value = canvas
      }, 3000)
    }
    function closeMemberCard() {
      memberCardDialog.value = false
      memberCardDialogSpinnerState.value = true
      memberCardOpenedMemberID.value = ''
    }
    function printMemberCard() {
      var memberCardPrintWindow = window.open('', 'PRINT', 'height=272,width=430')
      memberCardPrintWindow.document.write('<img src="' + memberCardOpenedCanvas.value.toDataURL() + '">')
      memberCardPrintWindow.print()
      memberCardPrintWindow.close()
    }
    function recoverPasswordConfirm(member) {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure you want to recover password for ' + member.NAMA_USER + '? This action cannot be undone.',
        cancel: true,
        persistent: true,
        ok: {
          color: 'primary',
          label: 'Recover'
        }
      }).onOk(() => {
        api.post('/autentikasi/recoverPassword', {
          ID_USER: member.ID_USER
        }, { withCredentials: true })
          .then(() => {
            $q.notify({
              message: 'Success recovering password',
              color: 'positive',
              icon: 'check_circle',
              position: 'top-right'
            })
            fetchMembers()
          })
          .catch(() => {
            $q.notify({
              message: 'An error occurred while recovering password',
              color: 'negative',
              icon: 'cancel',
              position: 'top-right'
            })
          })
      })
    }
    onMounted(() => {
      fetchMembers()
    })
    return {
      columns,
      rows,
      editData,
      editDialog,
      editDataPhotoHover,
      showEditDialog,
      editDataPhotoResizeCropAndConvertToBase64,
      closeEditDialog,
      deleteDataConfirm,
      saveEditData,
      newData,
      newDialog,
      newDataPhotoHover,
      showNewDialog,
      newDataPhotoResizeCropAndConvertToBase64,
      closeNewDialog,
      saveNewData,
      searchString,
      memberCardDialog,
      displayMemberCard,
      closeMemberCard,
      memberCardDialogSpinnerState,
      printMemberCard,
      memberCardOpenedCanvas,
      memberCardOpenedMemberID,
      recoverPasswordConfirm,
      fetchMembers
    }
  }
});
</script>
