<template>
  <div id="everything">
    <div class="col text-h3 q-mx-md q-mt-md q-mb-md self-center">
      Class Activity Reports
    </div>
    <div class="q-px-md">
      <div class="row">
        <div class="col-auto q-mt-sm">Year:&nbsp;&nbsp;&nbsp;&nbsp;</div>
        <div class="col-auto">
          <q-input
            v-model="year"
            dense
            borderless
            @keydown.enter.prevent="fetchClassActivityReports"
            @blur="fetchClassActivityReports"
            :rules="[(val) => /^\d+$/.test(val) || 'Invalid year']"
          >
            <q-tooltip anchor="center right" self="center end">
              Click to change year
            </q-tooltip>
          </q-input>
        </div>
      </div>
      <div class="row">
        <div class="col-auto q-mt-sm">Month:&nbsp;&nbsp;&nbsp;&nbsp;</div>
        <div class="col-auto">
          <q-input
            v-model="month"
            dense
            borderless
            @keydown.enter.prevent="fetchClassActivityReports"
            @blur="fetchClassActivityReports"
            :rules="[(val) => /^\d+$/.test(val) || 'Invalid month']"
          >
            <q-tooltip anchor="center right" self="center end">
              Click to change month
            </q-tooltip>
          </q-input>
        </div>
      </div>
      <div v-if="classActivity.length > 0">
        Generated at:&nbsp;&nbsp;&nbsp;&nbsp;{{
          new Date().toISOString().substring(0, 10)
        }}
      </div>
      <div v-if="classActivity.length == 0" class="q-px-md fixed-center">
        <q-spinner size="xl" />
      </div>
      <div v-if="classActivity.length > 0">
        <q-table
          class="q-mt-xl"
          :rows="classActivity"
          :columns="columns"
          :rows-per-page-options="[12]"
        >
        </q-table>
      </div>
    </div>
    <q-menu touch-position context-menu>
      <q-list dense>
        <q-item clickable v-close-popup @click="fetchClassActivityReports">
          <q-item-section>Refresh</q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="exportToPDF">
          <q-item-section>Export</q-item-section>
        </q-item>
      </q-list>
    </q-menu>
  </div>
</template>

<script>
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';
export default defineComponent({
  name: 'ClassActivityReportsComponent',
  setup() {
    const $q = useQuasar();
    const classActivity = ref([]);
    const year = ref(new Date().getFullYear());
    const month = ref(new Date().getMonth() + 1);
    const columns = [
      {
        name: 'class',
        required: false,
        label: 'Class',
        align: 'left',
        field: (row) => row.NAMA_KELAS,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'instructor',
        required: false,
        label: 'Instructor',
        align: 'left',
        field: (row) => row.NAMA_USER,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'numberOfMembers',
        required: false,
        label: 'Number of Members',
        align: 'left',
        field: (row) => row.JUMLAH_PESERTA,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'numberOfDayOff',
        required: false,
        label: 'Number of Day Off',
        align: 'left',
        field: (row) => row.JUMLAH_LIBUR,
        format: (val) => `${val}`,
        sortable: true,
      },
    ];
    async function fetchClassActivityReports() {
      if (/^\d+$/.test(year.value) && /^\d+$/.test(month.value)) {
        classActivity.value = [];
        return api
          .post(
            '/laporan/laporanAktivitasKelas',
            {
              TAHUN: year.value,
              BULAN: month.value,
            },
            {
              withCredentials: true,
            }
          )
          .then((response) => {
            $q.notify({
              message: 'Successfully generated classActivity reports',
              color: 'positive',
              position: 'top-right',
            });
            classActivity.value = response.data;
          })
          .catch(() => {
            $q.notify({
              message:
                'An error occurred while generating classActivity reports',
              color: 'negative',
              position: 'top-right',
            });
          });
      }
    }
    function exportToPDF() {
      const element = document.getElementById('everything');
      const doc = new jsPDF();
      html2canvas(element).then((canvas) => {
        const imageData = canvas.toDataURL('image/png', 1.0);
        const pdfWidth = doc.internal.pageSize.getWidth();
        const pdfHeight = doc.internal.pageSize.getHeight();
        const elementAspectRatio = canvas.width / canvas.height;
        let scaledWidth = pdfWidth;
        let scaledHeight = pdfWidth / elementAspectRatio;
        if (scaledHeight > pdfHeight) {
          scaledHeight = pdfHeight;
          scaledWidth = pdfHeight * elementAspectRatio;
        }
        doc.addImage(imageData, 'PNG', 0, 25, scaledWidth, scaledHeight);
        doc.save('GymActivityReport_' + new Date().toISOString() + '.pdf');
      });
    }
    onMounted(() => {
      fetchClassActivityReports();
    });
    return {
      classActivity,
      columns,
      year,
      month,
      fetchClassActivityReports,
      document,
      exportToPDF,
      Date,
    };
  },
});
</script>
