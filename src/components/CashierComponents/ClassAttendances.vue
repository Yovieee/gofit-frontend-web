<template>
  <div class="col text-h3 q-mx-md q-mt-md q-mb-md self-center">
    Class Attendances
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
          <template v-if="props.row.STATUS_PRESENSI_KELAS == 1">
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
                    ctx.fillText('Class Attendance Receipt', 25, 100);
                    ctx.font = '14px Courier New';
                    ctx.fillText(
                      'RECEIPT ID: ' + props.row.NO_STRUK_PRESENSI_KELAS,
                      25,
                      120
                    );
                    ctx.fillText(
                      'DATE      : ' + props.row.TANGGAL_PRESENSI_KELAS,
                      25,
                      140
                    );
                    ctx.fillText(
                      'MEMBER    : ' +
                        props.row.BOOKING.ID_MEMBER +
                        '/' +
                        props.row.BOOKING.NAMA_USER,
                      25,
                      160
                    );
                    ctx.fillText(
                      'CLASS     : ' + [props.row.BOOKING.JADWAL.NAMA_KELAS],
                      25,
                      180
                    );
                    ctx.fillText(
                      'INSTRUCTOR: ' + [props.row.BOOKING.JADWAL.NAMA_USER],
                      25,
                      200
                    );
                    ctx.fillText(
                      'CHARGE    : ' + [props.row.TARIF_PRESENSI_KELAS],
                      25,
                      220
                    );
                    ctx.fillText(
                      'REMAINING : ' + [props.row.SISA_DEPOSIT_PRESENSI_KELAS],
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
        </q-td>
      </template>
    </q-table>
  </div>
  <q-menu touch-position context-menu>
    <q-list dense>
      <q-item clickable v-close-popup @click="fetchClassAttendances">
        <q-item-section>Refresh</q-item-section>
      </q-item>
    </q-list>
  </q-menu>
</template>

<script>
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
export default defineComponent({
  name: 'GymAttendancesComponent',
  setup() {
    const $q = useQuasar();
    const columns = [
      {
        name: 'instructorName',
        required: false,
        label: 'Instructor Name',
        align: 'left',
        field: (row) => row.BOOKING.JADWAL.NAMA_USER,
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
        field: (row) => row.BOOKING.TANGGAL_BOOKING_KELAS,
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
        field: (row) => row.BOOKING.JADWAL.SESI_JADWAL,
        format: (val) =>
          `${
            [
              '06.00',
              '06.30',
              '07.00',
              '07.30',
              '08.00',
              '08.30',
              '09.00',
              '09.30',
              '10.00',
              '17.00',
              '17.30',
              '18.00',
              '18.30',
              '19.00',
              '19.30',
              '20.00',
              '20.30',
              '21.00',
            ][val]
          }`,
        sortable: true,
      },
      {
        name: 'status',
        required: false,
        label: 'Status',
        align: 'left',
        field: (row) => row.STATUS_PRESENSI_KELAS,
        format: (val) =>
          `${val == null ? 'Unassigned' : val == 0 ? 'Absent' : 'Attended'}`,
        sortable: true,
      },
      {
        name: 'updatedOn',
        required: false,
        label: 'Updated on',
        align: 'left',
        field: (row) => row.TANGGAL_PRESENSI_KELAS,
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
    async function fetchClassAttendances() {
      rows.value = [];
      return api
        .get('/presensiKelas', {
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
      fetchClassAttendances();
    });
    return {
      columns,
      rows,
      searchString,
      fetchClassAttendances,
      api,
      sessionStorage,
      window,
      Image,
    };
  },
});
</script>
