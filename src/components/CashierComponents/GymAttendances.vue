<template>
  <div class="col text-h3 q-mx-md q-mt-md q-mb-md self-center">
    Gym Attendances
  </div>
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
        <div class="col">
          <div class="row q-mb-xs">
            <q-input
              outlined
              dense
              v-model="searchString"
              label="Search here..."
            >
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
          </div>
          <div class="row q-gutter-md">
            <q-btn
              size="xs"
              dense
              flat
              label="All"
              @click="searchString = ''"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Unassigned"
              @click="searchString = 'Unassigned'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Absent"
              @click="searchString = 'Absent'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Attended"
              @click="searchString = 'Attended'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Late"
              @click="searchString = 'Late'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Not Yet Approved Permission"
              @click="searchString = 'Not Yet Approved Permission'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Permission Approved"
              @click="searchString = 'Permission Approved'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Permission Refused"
              @click="searchString = 'Permission Refused'"
            />
          </div>
        </div>
      </template>
      <template v-slot:body-cell-action="props">
        <q-td :props="props">
          <template v-if="props.row.STATUS_PRESENSI_MEMBER_GYM == 1">
            <q-btn
              class="q-mx-xs"
              flat
              round
              color="negative"
              icon="print"
              dense
              @click="
                () => {
                  var attendanceReceiptBackground = new Image();
                  attendanceReceiptBackground.onload = () => {
                    let receiptWindow = window.open(
                      '',
                      'PRINT',
                      'height=500,width=1000'
                    );
                    let canvas = receiptWindow.document.createElement('canvas');
                    canvas.width = 903;
                    canvas.height = 270;
                    receiptWindow.document.body.appendChild(canvas);
                    let ctx = canvas.getContext('2d');
                    ctx.globalCompositeOperation = 'darken';
                    ctx.drawImage(attendanceReceiptBackground, 0, 0);
                    ctx.font = '20px Arial';
                    ctx.fillText('Gym Attendance Receipt', 25, 140);
                    ctx.font = '14px Courier New';
                    ctx.fillText(
                      'RECEIPT ID: ' + props.row.NO_STRUK_PRESENSI_MEMBER_GYM,
                      25,
                      180
                    );
                    ctx.fillText(
                      'DATE      : ' + props.row.TANGGAL_PRESENSI_MEMBER_GYM,
                      25,
                      200
                    );
                    ctx.fillText(
                      'MEMBER    : ' +
                        props.row.BOOKING.ID_MEMBER +
                        '/' +
                        props.row.BOOKING.NAMA_USER,
                      25,
                      220
                    );
                    ctx.fillText(
                      'SESSION   : ' +
                        [
                          '07.00-09.00',
                          '09.00-11.00',
                          '11.00-13.00',
                          '13.00-15.00',
                          '15.00-17.00',
                          '17.00-19.00',
                          '19.00-21.00',
                        ][props.row.BOOKING.SESI_BOOKING_GYM],
                      25,
                      240
                    );
                    receiptWindow.print();
                    receiptWindow.close();
                  };
                  attendanceReceiptBackground.src =
                    'src/assets/attendance-receipt.jpg';
                }
              "
            />
          </template>
          <template v-if="props.row.STATUS_PRESENSI_MEMBER_GYM == null">
            <q-btn
              class="q-mx-xs"
              color="negative"
              label="Absent"
              dense
              @click="
                $q.dialog({
                  title: 'Confirm',
                  message: 'Are you sure you want to absent this attendance?',
                  cancel: true,
                  persistent: true,
                }).onOk(() => {
                  api
                    .post(
                      'presensiMemberGym/absenkan',
                      {
                        NO_STRUK_PRESENSI_MEMBER_GYM:
                          props.row.NO_STRUK_PRESENSI_MEMBER_GYM,
                        ID_USER: JSON.parse(sessionStorage.getItem('user'))
                          .ID_USER,
                        ID_PEGAWAI: JSON.parse(sessionStorage.getItem('user'))
                          .ID_PEGAWAI,
                      },
                      { withCredentials: true }
                    )
                    .then(() => {
                      fetchGymAttendances();
                      $q.notify({
                        message: 'Attendance Absented',
                        color: 'positive',
                        position: 'top-right',
                      });
                    })
                    .catch(() => {
                      $q.notify({
                        message: 'An error occurred while absenting attendance',
                        color: 'negative',
                        position: 'top-right',
                      });
                    });
                })
              "
            />
            <q-btn
              class="q-mx-xs"
              color="positive"
              label="Attended"
              dense
              @click="
                $q.dialog({
                  title: 'Confirm',
                  message: 'Are you sure you want to attend this attendance?',
                  cancel: true,
                  persistent: true,
                }).onOk(() => {
                  api
                    .post(
                      'presensiMemberGym/hadirkan',
                      {
                        NO_STRUK_PRESENSI_MEMBER_GYM:
                          props.row.NO_STRUK_PRESENSI_MEMBER_GYM,
                        ID_USER: JSON.parse(sessionStorage.getItem('user'))
                          .ID_USER,
                        ID_PEGAWAI: JSON.parse(sessionStorage.getItem('user'))
                          .ID_PEGAWAI,
                      },
                      { withCredentials: true }
                    )
                    .then(() => {
                      fetchGymAttendances();
                      $q.notify({
                        message: 'Attendance Attended',
                        color: 'positive',
                        position: 'top-right',
                      });
                    })
                    .catch(() => {
                      $q.notify({
                        message: 'An error occurred while attending attendance',
                        color: 'negative',
                        position: 'top-right',
                      });
                    });
                })
              "
            />
          </template>
        </q-td>
      </template>
    </q-table>
  </div>
  <q-menu touch-position context-menu>
    <q-list dense>
      <q-item clickable v-close-popup @click="fetchGymAttendances">
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
  name: 'GymAttendancesComponent',
  setup() {
    const $q = useQuasar();
    const columns = [
      {
        name: 'employeeName',
        required: false,
        label: 'Employee Name',
        align: 'left',
        field: (row) => row.NAMA_USER,
        format: (val) => `${val == null ? 'Unassigned' : val}`,
        sortable: true,
      },
      {
        name: 'memberName',
        required: false,
        label: 'Member Name',
        align: 'left',
        field: (row) => row.BOOKING.NAMA_USER,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'date',
        required: false,
        label: 'Date',
        align: 'left',
        field: (row) => row.BOOKING.TANGGAL_BOOKING_GYM,
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
        name: 'session',
        required: false,
        label: 'Session',
        align: 'left',
        field: (row) => row.BOOKING.SESI_BOOKING_GYM,
        format: (val) =>
          `${
            [
              '07.00-09.00',
              '09.00-11.00',
              '11.00-13.00',
              '13.00-15.00',
              '15.00-17.00',
              '17.00-19.00',
              '19.00-21.00',
            ][val]
          }`,
        sortable: true,
      },
      {
        name: 'status',
        required: false,
        label: 'Status',
        align: 'left',
        field: (row) => row.STATUS_PRESENSI_MEMBER_GYM,
        format: (val) =>
          `${val == null ? 'Unassigned' : val == 0 ? 'Absent' : 'Attended'}`,
        sortable: true,
      },
      {
        name: 'updatedOn',
        required: false,
        label: 'Updated on',
        align: 'left',
        field: (row) => row.TANGGAL_PRESENSI_MEMBER_GYM,
        format: (val) => `${val == null ? 'Unassigned' : val}`,
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
    const searchString = ref('');
    async function fetchGymAttendances() {
      rows.value = [];
      return api
        .get('/presensiMemberGym', {
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
    onMounted(() => {
      fetchGymAttendances();
    });
    return {
      columns,
      rows,
      searchString,
      fetchGymAttendances,
      api,
      sessionStorage,
      window,
      Image,
    };
  },
});
</script>
