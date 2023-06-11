<template>
  <div class="col text-h3 q-mx-md q-mt-md q-mb-md self-center">Instructors</div>
  <div class="q-px-md">
    <div v-if="rows.length == 0" class="q-px-md fixed-center">
      <q-spinner size="xl" />
    </div>
    <q-table
      :filter="searchString"
      :rows="rows"
      :columns="columns"
      row-key="id"
      :rows-per-page-options="[6]"
      v-if="rows.length != 0"
    >
      <template v-slot:top>
        <q-input outlined dense v-model="searchString" label="Search here...">
          <template v-slot:append>
            <q-icon
              v-if="searchString !== ''"
              name="close"
              @click="searchString = ''"
              class="cursor-pointer"
            />
            <q-icon name="search" />
          </template>
        </q-input>
        <q-space />
        <q-btn
          flat
          icon="add"
          color="primary"
          label="New"
          @click="showNewDialog"
        />
      </template>
      <template v-slot:body-cell-photo="props">
        <q-td :props="props">
          <q-avatar>
            <q-icon
              v-if="
                props.row.FOTO_USER == null || props.row.FOTO_USER.length == 0
              "
              name="account_circle"
              size="xl"
              color="grey"
            />
            <q-img
              v-if="
                !(
                  props.row.FOTO_USER == null || props.row.FOTO_USER.length == 0
                )
              "
              :src="'data:image/jpeg;base64,' + props.row.FOTO_USER"
            />
          </q-avatar>
        </q-td>
      </template>
      <template v-slot:body-cell-action="props">
        <q-td :props="props">
          <q-btn
            flat
            round
            color="positive"
            icon="edit"
            @click="showEditDialog(props.row)"
          />
          <q-btn
            flat
            round
            color="negative"
            icon="delete"
            @click="deleteDataConfirm(props.row)"
          />
        </q-td>
      </template>
    </q-table>
  </div>
  <q-dialog v-model="editDialog" persistent>
    <q-card style="min-width: 15cm">
      <q-form
        @submit="
          $q.dialog({
            title: 'Confirm',
            message: 'Are you sure you want to save this data?',
            cancel: true,
            persistent: true,
          }).onOk(() => {
            saveEditData();
          })
        "
      >
        <q-card-section class="q-pb-none">
          <div class="text-h6">Edit Instructor</div>
        </q-card-section>
        <div class="row">
          <div class="col relative-position">
            <div class="absolute-center">
              <q-card-section class="q-pb-none">
                <input
                  id="editDataPhotoInput"
                  ref="editDataPhotoInput"
                  @input="editDataPhotoResizeCropAndConvertToBase64()"
                  type="file"
                  accept="image/*"
                  hidden
                />
                <div class="text-center">
                  <q-avatar style="height: 5cm; width: 5cm">
                    <q-btn
                      round
                      icon="add_a_photo"
                      style="height: 5cm; width: 5cm"
                      color="grey"
                      @click="this.$refs.editDataPhotoInput.click()"
                      v-if="editDataPhotoHover"
                      @mouseleave="editDataPhotoHover = false"
                    />
                    <q-btn
                      v-if="
                        (editData.photo.value == null ||
                          editData.photo.value.length == 0) &&
                        !editDataPhotoHover
                      "
                      icon="account_circle"
                      style="height: 5cm; width: 5cm"
                      color="grey"
                      @mouseenter="editDataPhotoHover = true"
                    />
                    <img
                      style="height: 5cm; width: 5cm"
                      v-if="
                        !(
                          editData.photo.value == null ||
                          editData.photo.value.length == 0
                        ) && !editDataPhotoHover
                      "
                      :src="'data:image/jpeg;base64,' + editData.photo.value"
                      @mouseenter="editDataPhotoHover = true"
                    />
                  </q-avatar>
                </div>
              </q-card-section>
            </div>
          </div>
          <div class="col">
            <q-card-section class="q-pb-xs">
              <q-input
                dense
                outlined
                v-model="editData.id.value"
                label="ID"
                disable
              />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-lg">
              <q-input
                dense
                outlined
                v-model="editData.name.value"
                label="Name"
                :rules="[(val) => !!val || 'Name is missing']"
              />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input
                dense
                outlined
                v-model="editData.email.value"
                label="Email"
                :rules="[
                  (val) => !!val || 'Email is missing',
                  (val) =>
                    /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/.test(
                      val
                    ) || 'Invalid email',
                ]"
              />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input
                dense
                outlined
                v-model="editData.dateOfBirth.value"
                mask="date"
                :rules="[(val) => !!val || 'Date of Birth is missing', 'date']"
                label="Date of Birth"
              >
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy
                      cover
                      transition-show="scale"
                      transition-hide="scale"
                    >
                      <q-date v-model="editData.dateOfBirth.value">
                        <div class="row items-center justify-end">
                          <q-btn
                            v-close-popup
                            label="Close"
                            color="primary"
                            flat
                          />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input
                dense
                type="textarea"
                outlined
                v-model="editData.description.value"
                label="Description"
                :rules="[(val) => !!val || 'Description is missing']"
              />
            </q-card-section>
          </div>
        </div>
        <q-card-section>
          <div class="row justify-end">
            <q-btn
              class="q-mr-sm"
              label="Cancel"
              color="primary"
              flat
              @click="closeEditDialog()"
            />
            <q-btn type="submit" label="Save" color="primary" />
          </div>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
  <q-dialog v-model="newDialog" persistent>
    <q-card style="min-width: 15cm">
      <q-form
        @submit="
          $q.dialog({
            title: 'Confirm',
            message: 'Are you sure you want to save this data?',
            cancel: true,
            persistent: true,
          }).onOk(() => {
            saveNewData();
          })
        "
      >
        <q-card-section class="q-pb-none">
          <div class="text-h6">New Instructor</div>
        </q-card-section>
        <div class="row">
          <div class="col relative-position">
            <div class="absolute-center">
              <q-card-section class="q-pb-none">
                <input
                  id="newDataPhotoInput"
                  ref="newDataPhotoInput"
                  @input="newDataPhotoResizeCropAndConvertToBase64()"
                  type="file"
                  accept="image/*"
                  hidden
                />
                <div class="text-center">
                  <q-avatar style="height: 5cm; width: 5cm">
                    <q-btn
                      round
                      icon="add_a_photo"
                      style="height: 5cm; width: 5cm"
                      color="grey"
                      @click="this.$refs.newDataPhotoInput.click()"
                      v-if="newDataPhotoHover"
                      @mouseleave="newDataPhotoHover = false"
                    />
                    <q-btn
                      v-if="newData.photo.value == null && !newDataPhotoHover"
                      icon="account_circle"
                      style="height: 5cm; width: 5cm"
                      color="grey"
                      @mouseenter="newDataPhotoHover = true"
                    />
                    <img
                      style="height: 5cm; width: 5cm"
                      v-if="newData.photo.value != null && !newDataPhotoHover"
                      :src="'data:image/jpeg;base64,' + newData.photo.value"
                      @mouseenter="newDataPhotoHover = true"
                    />
                  </q-avatar>
                </div>
              </q-card-section>
            </div>
          </div>
          <div class="col">
            <q-card-section
              class="q-pb-xs"
              :style="
                newData.id.value.length == 0
                  ? 'display: block'
                  : 'display: none'
              "
            >
              <q-field label="ID" outlined dense stack-label>
                <template v-slot:control>
                  <div class="self-center full-width no-outline" tabindex="0">
                    <q-skeleton type="text" />
                  </div>
                </template>
              </q-field>
            </q-card-section>
            <q-card-section
              class="q-pb-xs"
              :style="
                newData.id.value.length != 0
                  ? 'display: block'
                  : 'display: none'
              "
            >
              <q-input
                dense
                outlined
                v-model="newData.id.value"
                label="ID"
                disable
              />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-lg">
              <q-input
                dense
                outlined
                v-model="newData.name.value"
                label="Name"
                :rules="[(val) => !!val || 'Name is missing']"
              />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input
                dense
                outlined
                v-model="newData.email.value"
                label="Email"
                :rules="[
                  (val) => !!val || 'Email is missing',
                  (val) =>
                    /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/.test(
                      val
                    ) || 'Invalid email',
                ]"
              />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input
                dense
                outlined
                v-model="newData.dateOfBirth.value"
                mask="date"
                :rules="[(val) => !!val || 'Date of Birth is missing', 'date']"
                label="Date of Birth"
              >
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy
                      cover
                      transition-show="scale"
                      transition-hide="scale"
                    >
                      <q-date v-model="newData.dateOfBirth.value">
                        <div class="row items-center justify-end">
                          <q-btn
                            v-close-popup
                            label="Close"
                            color="primary"
                            flat
                          />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input
                dense
                type="textarea"
                outlined
                v-model="newData.description.value"
                label="Description"
                :rules="[(val) => !!val || 'Description is missing']"
              />
            </q-card-section>
          </div>
        </div>
        <q-card-section>
          <div class="row justify-end">
            <q-btn
              class="q-mr-sm"
              label="Cancel"
              color="primary"
              flat
              @click="closeNewDialog()"
            />
            <q-btn type="submit" label="Save" color="primary" />
          </div>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
  <q-menu touch-position context-menu>
    <q-list dense>
      <q-item clickable v-close-popup @click="fetchInstructors">
        <q-item-section>Refresh</q-item-section>
      </q-item>
    </q-list>
  </q-menu>
</template>

<script>
import { nanoid } from 'nanoid';
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
export default defineComponent({
  name: 'InstructorsComponent',
  setup() {
    const $q = useQuasar();
    const columns = [
      {
        name: 'id',
        required: false,
        label: 'ID',
        align: 'left',
        field: (row) => row.ID_INSTRUKTUR,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'photo',
        required: false,
        label: 'Photo',
        align: 'left',
        sortable: false,
      },
      {
        name: 'name',
        required: false,
        label: 'Name',
        align: 'left',
        field: (row) => row.NAMA_USER,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'email',
        required: false,
        label: 'Email',
        align: 'left',
        field: (row) => row.EMAIL_USER,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'dateOfBirth',
        required: false,
        label: 'Date of Birth',
        align: 'left',
        field: (row) => row.TANGGAL_LAHIR_USER,
        format: (val) =>
          `${
            [
              'Sunday',
              'Monday',
              'Tuesday',
              'Wednesday',
              'Thursday',
              'Friday',
              'Saturday',
            ][new Date(val).getDay()] +
            ', ' +
            new Date(val).getDate() +
            ' ' +
            new Date(val).toLocaleString('default', { month: 'long' }) +
            ' ' +
            new Date(val).getFullYear()
          }`,
        sortable: true,
      },
      {
        name: 'description',
        required: false,
        label: 'Description',
        align: 'left',
        field: (row) => row.DESKRIPSI_INSTRUKTUR,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'action',
        required: false,
        label: 'Action',
        align: 'left',
      },
    ];
    const rows = ref([]);
    const editDialog = ref(false);
    const editData = {
      id_user: ref(''),
      id: ref(''),
      photo: ref(''),
      name: ref(''),
      email: ref(''),
      dateOfBirth: ref(''),
      description: ref(''),
    };
    const editDataPhotoHover = ref(false);
    const newDialog = ref(false);
    const newData = {
      id_user: ref(''),
      id: ref(''),
      photo: ref(null),
      name: ref(''),
      email: ref(''),
      dateOfBirth: ref(''),
      description: ref(''),
    };
    const newDataPhotoHover = ref(false);
    const searchString = ref('');
    function fetchInstructors() {
      rows.value = [];
      api
        .get('/instruktur', {
          withCredentials: true,
        })
        .then((response) => {
          rows.value = response.data;
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while fetching users',
            color: 'negative',
            position: 'top-right',
          });
        });
    }
    function generateID() {
      api
        .get('/instruktur/generateID', {
          withCredentials: true,
        })
        .then((response) => {
          newData.id.value = response.data[0];
          newData.id_user.value = nanoid(32);
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while generating ID',
            color: 'negative',
            position: 'top-right',
          });
          newDialog.value = false;
        });
    }
    function showEditDialog(user) {
      editDialog.value = true;
      editData.id_user.value = user.ID_USER;
      editData.id.value = user.ID_INSTRUKTUR;
      editData.photo.value = user.FOTO_USER;
      editData.name.value = user.NAMA_USER;
      editData.email.value = user.EMAIL_USER;
      editData.dateOfBirth.value = user.TANGGAL_LAHIR_USER.substring(0, 10)
        .replace('-', '/')
        .replace('-', '/');
      editData.description.value = user.DESKRIPSI_INSTRUKTUR;
    }
    function editDataPhotoResizeCropAndConvertToBase64() {
      var fileInput = document.getElementById('editDataPhotoInput');
      var file = fileInput.files[0];
      var reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = function (event) {
        var img = new Image();
        img.src = event.target.result;
        img.onload = function () {
          var canvas = document.createElement('canvas');
          var ctx = canvas.getContext('2d');
          var width = 256;
          var height = 256;
          var offsetX = 0;
          var offsetY = 0;
          if (img.width > img.height) {
            height = Math.round(img.height * (width / img.width));
            offsetY = Math.round((256 - height) / 2);
          } else {
            width = Math.round(img.width * (height / img.height));
            offsetX = Math.round((256 - width) / 2);
          }
          canvas.width = 256;
          canvas.height = 256;
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(0, 0, 256, 256);
          ctx.drawImage(img, offsetX, offsetY, width, height);
          var base64 = canvas.toDataURL('image/jpeg');
          editData.photo.value = base64.substring(23);
        };
      };
    }
    function closeEditDialog() {
      editDialog.value = false;
      editData.id_user.value = '';
      editData.id.value = '';
      editData.photo.value = null;
      editData.name.value = '';
      editData.email.value = '';
      editData.dateOfBirth.value = '';
      editData.description.value = '';
    }

    function showNewDialog() {
      newDialog.value = true;
      generateID();
    }
    function newDataPhotoResizeCropAndConvertToBase64() {
      var fileInput = document.getElementById('newDataPhotoInput');
      var file = fileInput.files[0];
      var reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = function (event) {
        var img = new Image();
        img.src = event.target.result;
        img.onload = function () {
          var canvas = document.createElement('canvas');
          var ctx = canvas.getContext('2d');
          var width = 256;
          var height = 256;
          var offsetX = 0;
          var offsetY = 0;
          if (img.width > img.height) {
            height = Math.round(img.height * (width / img.width));
            offsetY = Math.round((256 - height) / 2);
          } else {
            width = Math.round(img.width * (height / img.height));
            offsetX = Math.round((256 - width) / 2);
          }
          canvas.width = 256;
          canvas.height = 256;
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(0, 0, 256, 256);
          ctx.drawImage(img, offsetX, offsetY, width, height);
          var base64 = canvas.toDataURL('image/jpeg');
          newData.photo.value = base64.substring(23);
        };
      };
    }
    function closeNewDialog() {
      newDialog.value = false;
      newData.id_user.value = '';
      newData.id.value = '';
      newData.photo.value = null;
      newData.name.value = '';
      newData.email.value = '';
      newData.dateOfBirth.value = '';
      newData.description.value = '';
    }
    function deleteData(id_instruktur, id_user) {
      api
        .post(
          '/instruktur/delete',
          {
            ID_INSTRUKTUR: id_instruktur,
            ID_USER: id_user,
          },
          { withCredentials: true }
        )
        .then(() => {
          $q.notify({
            message: 'Success deleting user',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right',
          });
          fetchInstructors();
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while deleting user',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right',
          });
        });
    }
    function deleteDataConfirm(user) {
      $q.dialog({
        title: 'Confirm',
        message:
          'Are you sure you want to delete ' +
          user.NAMA_USER +
          '? This action cannot be undone.',
        cancel: true,
        persistent: true,
        ok: {
          color: 'negative',
          label: 'Delete',
        },
      }).onOk(() => {
        deleteData(user.ID_INSTRUKTUR, user.ID_USER);
      });
    }
    function saveEditData() {
      api
        .post(
          '/instruktur/update',
          {
            ID_USER: editData.id_user.value,
            ID_INSTRUKTUR: editData.id.value,
            NAMA_USER: editData.name.value,
            EMAIL_USER: editData.email.value,
            TANGGAL_LAHIR_USER: editData.dateOfBirth.value,
            DESKRIPSI_INSTRUKTUR: editData.description.value,
            FOTO_USER: editData.photo.value,
          },
          { withCredentials: true }
        )
        .then(() => {
          $q.notify({
            message: 'Success editing user',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right',
          });
          fetchInstructors();
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while editing user',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right',
          });
        });
      closeEditDialog();
    }
    function saveNewData() {
      api
        .post(
          '/instruktur/create',
          {
            ID_USER: newData.id_user.value,
            ID_INSTRUKTUR: newData.id.value,
            NAMA_USER: newData.name.value,
            EMAIL_USER: newData.email.value,
            TANGGAL_LAHIR_USER: newData.dateOfBirth.value,
            DESKRIPSI_INSTRUKTUR: newData.description.value,
            FOTO_USER: newData.photo.value,
          },
          { withCredentials: true }
        )
        .then(() => {
          $q.notify({
            message: 'Success adding user',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right',
          });
          fetchInstructors();
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while adding user',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right',
          });
        });
      closeNewDialog();
    }
    onMounted(() => {
      fetchInstructors();
    });
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
      fetchInstructors,
    };
  },
});
</script>
