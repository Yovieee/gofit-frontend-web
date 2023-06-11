<template>
  <div class="col text-h3 q-mx-md q-mt-md q-mb-md self-center">Members</div>
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
            color="blue"
            icon="receipt_long"
            @click="showTransactionDialog(props.row)"
          />
          <q-btn
            flat
            round
            color="orange"
            icon="key"
            @click="recoverPasswordConfirm(props.row)"
          />
          <q-btn
            flat
            round
            color="purple"
            icon="badge"
            @click="displayMemberCard(props.row)"
          />
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
          <div class="text-h6">Edit Member</div>
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
                outlined
                v-model="editData.address.value"
                label="Address"
                :rules="[(val) => !!val || 'Address is missing']"
              />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input
                dense
                outlined
                v-model="editData.phone.value"
                label="Phone"
                :rules="[
                  (val) => !!val || 'Phone is missing',
                  (val) => /^\d+$/.test(val) || 'Invalid phone',
                ]"
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
          <div class="text-h6">New Member</div>
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
                outlined
                v-model="newData.address.value"
                label="Address"
                :rules="[(val) => !!val || 'Address is missing']"
              />
            </q-card-section>
            <q-card-section class="q-pb-none q-pt-sm">
              <q-input
                dense
                outlined
                v-model="newData.phone.value"
                label="Phone"
                :rules="[
                  (val) => !!val || 'Phone is missing',
                  (val) => /^\d+$/.test(val) || 'Invalid phone',
                ]"
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
  <q-dialog persistent v-model="memberCardDialog">
    <q-card>
      <q-card-section class="q-pb-none">
        <div class="row">
          <div class="col">
            <div class="text-h6">Member Card</div>
          </div>
          <div class="col-auto q-pr-sm">
            <a
              v-if="!memberCardDialogSpinnerState"
              :href="
                memberCardOpenedCanvas
                  .toDataURL('image/png')
                  .replace('image/png', 'image/octet-stream')
              "
              :download="'membercard_(' + memberCardOpenedMemberID + ').png'"
              style="color: black"
            >
              <q-icon
                class="cursor-pointer q-mt-xs"
                size="sm"
                name="download"
              />
            </a>
          </div>
          <div class="col-auto q-px-sm">
            <q-icon
              v-if="!memberCardDialogSpinnerState"
              class="cursor-pointer q-mt-xs"
              size="sm"
              name="print"
              @click="printMemberCard"
            />
          </div>
          <div class="col-auto q-pl-sm">
            <q-icon
              class="cursor-pointer q-mt-xs"
              size="sm"
              name="close"
              @click="closeMemberCard"
            />
          </div>
        </div>
      </q-card-section>
      <q-card-section class="q-pt-sm">
        <div
          v-if="memberCardDialogSpinnerState"
          style="width: 428px"
          class="text-center"
        >
          <q-spinner />
        </div>
        <div id="memberCardDisplay"></div>
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-dialog v-model="transactionDialog">
    <q-card>
      <template v-if="rows.length > 0">
        <q-tabs
          v-model="transactionsTab"
          dense
          class="text-grey"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator
        >
          <q-tab name="activations" label="Activations" />
          <q-tab name="money-deposits" label="Money Deposits" />
          <q-tab name="class-deposits" label="Class Deposits" />
        </q-tabs>
        <q-separator />
        <q-tab-panels v-model="transactionsTab" animated>
          <q-tab-panel class="q-pa-none" name="activations">
            <div>
              <q-list>
                <q-item
                  v-if="
                    rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                      .MEMBERSHIP_TRANSACTIONS.length == 0
                  "
                >
                  <q-item-section>
                    <q-item-label> No transaction </q-item-label>
                  </q-item-section>
                </q-item>
                <q-expansion-item
                  :label="activation.TANGGAL_AKTIVASI_MEMBERSHIP"
                  v-for="(activation, index) in rows.filter(
                    (m) => m.ID_MEMBER == selectedMemberID
                  )[0].MEMBERSHIP_TRANSACTIONS"
                  :key="index"
                >
                  <q-list class="q-pl-lg">
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>ID</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          activation.ID_MEMBERSHIP
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Activated on</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          activation.TANGGAL_AKTIVASI_MEMBERSHIP
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Expired on</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          activation.TANGGAL_KADALUARSA_MEMBERSHIP
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Cashier</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label
                          >{{ activation.NAMA_USER }}
                          <q-chip
                            dense
                            color="secondary"
                            class="text-white q-ma-none"
                            size="sm"
                            >{{ activation.ID_PEGAWAI }}</q-chip
                          ></q-item-label
                        >
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>
                          <q-icon
                            class="float-right cursor-pointer"
                            name="printer"
                            @click="
                              printActivationReceipt(
                                activation.ID_MEMBER,
                                activation.ID_MEMBERSHIP
                              )
                            "
                          />
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </q-expansion-item>
              </q-list>
            </div>

            <q-toolbar class="bg-secondary text-white text-no-wrap">
              {{
                rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                  .MEMBERSHIP_TRANSACTIONS.length == 0
                  ? 'This member is currently inactive'
                  : rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                      .MEMBERSHIP_TRANSACTIONS[0]
                      .TANGGAL_KADALUARSA_MEMBERSHIP >
                    new Date().toISOString().replace('T', ' ').substring(0, 19)
                  ? 'This member is currently active'
                  : 'This member is currently inactive'
              }}
              <q-space />
              <q-btn
                rounded
                dense
                size="md"
                class="q-pr-sm"
                v-if="
                  rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                    .MEMBERSHIP_TRANSACTIONS.length == 0 ||
                  !(
                    rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                      .MEMBERSHIP_TRANSACTIONS[0]
                      .TANGGAL_KADALUARSA_MEMBERSHIP >
                    new Date().toISOString().replace('T', ' ').substring(0, 19)
                  )
                "
                @click="
                  $q.dialog({
                    title: 'Confirm',
                    message:
                      'Are you sure you want to activate ' +
                      rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                        .NAMA_USER +
                      '? This member will remain active until ' +
                      new Date(
                        new Date().getFullYear() + 1,
                        new Date().getMonth(),
                        new Date().getDate()
                      ).toDateString(),
                    cancel: true,
                    persistent: true,
                  }).onOk(() => {
                    api
                      .post('membership/create', {
                        MEM_ID_USER: rows.filter(
                          (m) => m.ID_MEMBER == selectedMemberID
                        )[0].ID_USER,
                        ID_MEMBER: selectedMemberID,
                        PEG_ID_USER: JSON.parse(sessionStorage.getItem('user'))
                          .ID_USER,
                        ID_PEGAWAI: JSON.parse(sessionStorage.getItem('user'))
                          .ID_PEGAWAI,
                        TANGGAL_AKTIVASI_MEMBERSHIP: new Date(
                          new Date().getFullYear(),
                          new Date().getMonth(),
                          new Date().getDate(),
                          new Date().getHours() + 7,
                          new Date().getMinutes(),
                          new Date().getSeconds(),
                          new Date().getMilliseconds()
                        )
                          .toISOString()
                          .replace('T', ' ')
                          .substring(0, 19),
                        TANGGAL_KADALUARSA_MEMBERSHIP: new Date(
                          new Date().getFullYear() + 1,
                          new Date().getMonth(),
                          new Date().getDate()
                        )
                          .toISOString()
                          .replace('T', ' ')
                          .substring(0, 19),
                      })
                      .then((response) => {
                        $q.notify({
                          color: 'positive',
                          position: 'top-right',
                          message: 'Membership activated successfully',
                        });
                        fetchMembers();
                      })
                      .catch(() => {
                        $q.notify({
                          color: 'negative',
                          position: 'top-right',
                          message: 'Failed to activate membership',
                        });
                      });
                  })
                "
                icon="autorenew"
                color="white"
                text-color="primary"
                label="Activate Now"
              />
            </q-toolbar>
          </q-tab-panel>

          <q-tab-panel class="q-pa-none" name="money-deposits">
            <div>
              <q-list>
                <q-item
                  v-if="
                    rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                      .MONEY_DEPOSIT_TRANSACTIONS.length == 0
                  "
                >
                  <q-item-section>
                    <q-item-label> No transaction </q-item-label>
                  </q-item-section>
                </q-item>
                <q-expansion-item
                  :label="money.TANGGAL_DEPOSIT_UANG"
                  v-for="(money, index) in rows.filter(
                    (m) => m.ID_MEMBER == selectedMemberID
                  )[0].MONEY_DEPOSIT_TRANSACTIONS"
                  :key="index"
                >
                  <q-list class="q-pl-lg">
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>ID</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          money.NO_STRUK_DEPOSIT_UANG
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Cash</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          'Rp ' + money.JUMLAH_DEPOSIT_UANG
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Bonus</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          'Rp ' + money.BONUS_DEPOSIT_UANG
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Remaining</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          'Rp ' + money.SISA_DEPOSIT_UANG
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Cashier</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label
                          >{{ money.NAMA_USER }}
                          <q-chip
                            dense
                            color="secondary"
                            class="text-white q-ma-none"
                            size="sm"
                            >{{ money.ID_PEGAWAI }}</q-chip
                          ></q-item-label
                        >
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>
                          <q-icon
                            class="float-right cursor-pointer"
                            name="printer"
                            @click="
                              printMoneyDepositReceipt(
                                money.ID_MEMBER,
                                money.NO_STRUK_DEPOSIT_UANG
                              )
                            "
                          />
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </q-expansion-item>
              </q-list>
            </div>

            <q-toolbar class="bg-secondary text-white text-no-wrap">
              {{
                rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                  .MONEY_DEPOSIT_TRANSACTIONS.length == 0
                  ? 'This member has Rp 0,00 money deposit'
                  : 'This member has Rp ' +
                    rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                      .MONEY_DEPOSIT_TRANSACTIONS[0].SISA_DEPOSIT_UANG +
                    ',00 money deposit'
              }}
              <q-btn
                rounded
                dense
                size="md"
                class="q-pr-sm q-ml-md"
                @click="
                  showNewMoneyDepositDialog(
                    rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                  )
                "
                icon="attach_money"
                color="white"
                text-color="primary"
                label="New Money Deposit"
              />
            </q-toolbar>
          </q-tab-panel>

          <q-tab-panel class="q-pa-none" name="class-deposits">
            <div>
              <q-list>
                <q-item
                  v-if="
                    rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                      .CLASS_DEPOSIT_TRANSACTIONS.length == 0
                  "
                >
                  <q-item-section>
                    <q-item-label> No transaction </q-item-label>
                  </q-item-section>
                </q-item>
                <q-expansion-item
                  :label="fitnessClass.TANGGAL_DEPOSIT_KELAS"
                  v-for="(fitnessClass, index) in rows.filter(
                    (m) => m.ID_MEMBER == selectedMemberID
                  )[0].CLASS_DEPOSIT_TRANSACTIONS"
                  :key="index"
                >
                  <q-list class="q-pl-lg">
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>ID</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          fitnessClass.NO_STRUK_DEPOSIT_KELAS
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Cash</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          'Rp ' + fitnessClass.JUMLAH_BAYAR_DEPOSIT_KELAS
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Amount</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          fitnessClass.JUMLAH_DEPOSIT_KELAS
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Bonus</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          fitnessClass.BONUS_DEPOSIT_KELAS
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Class</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          fitnessClass.NAMA_KELAS
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Expired on</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label>{{
                          fitnessClass.TANGGAL_KADALUARSA_DEPOSIT_KELAS
                        }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item dense class="text-caption">
                      <q-item-section>
                        <q-item-label>Cashier</q-item-label>
                      </q-item-section>
                      <q-item-section side>
                        <q-item-label
                          >{{ fitnessClass.NAMA_USER }}
                          <q-chip
                            dense
                            color="secondary"
                            class="text-white q-ma-none"
                            size="sm"
                            >{{ fitnessClass.ID_PEGAWAI }}</q-chip
                          ></q-item-label
                        >
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>
                          <q-icon
                            class="float-right cursor-pointer"
                            name="printer"
                            @click="
                              printClassDepositReceipt(
                                fitnessClass.ID_MEMBER,
                                fitnessClass.NO_STRUK_DEPOSIT_KELAS
                              )
                            "
                          />
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </q-expansion-item>
              </q-list>
            </div>

            <q-toolbar class="bg-secondary text-white text-no-wrap">
              <q-space />
              <q-btn
                rounded
                dense
                size="md"
                class="q-pr-sm q-ml-md"
                @click="
                  showNewClassDepositDialog(
                    rows.filter((m) => m.ID_MEMBER == selectedMemberID)[0]
                  )
                "
                icon="school"
                color="white"
                text-color="primary"
                label="New Class Deposit"
              />
            </q-toolbar>
          </q-tab-panel>
        </q-tab-panels>
      </template>
      <template v-if="rows.length == 0">
        <q-spinner />
      </template>
    </q-card>
  </q-dialog>
  <q-dialog v-model="newMoneyDepositDialog">
    <q-card>
      <q-form @submit="saveMoneyDepositData">
        <q-card-section class="q-pb-none">
          <div class="text-h6">New Money Deposit Transaction</div>
        </q-card-section>
        <q-card-section class="q-pt-sm q-pb-none">
          <q-input
            dense
            outlined
            v-model="newMoneyDepositData.cash.value"
            :rules="[(val) => !!val || 'Cash is missing']"
            label="Cash"
          />
        </q-card-section>
        <q-card-section class="q-py-none">
          This transaction will get bonus Rp
          {{
            parseInt(newMoneyDepositData.cash.value) >=
            promos.filter((p) => p.JENIS_PROMO == 0)[0].MINIMAL_TRANSAKSI_PROMO
              ? promos.filter((p) => p.JENIS_PROMO == 0)[0]
                  .BONUS_TRANSAKSI_PROMO
              : 0
          }}
        </q-card-section>
        <q-card-section>
          <div class="row justify-end">
            <q-btn type="submit" color="primary" label="Save" />
          </div>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
  <q-dialog v-model="newClassDepositDialog">
    <q-card>
      <q-form @submit="saveClassDepositData">
        <q-card-section class="q-pb-none">
          <div class="text-h6">New Class Deposit Transaction</div>
        </q-card-section>
        <q-card-section class="q-pt-sm q-pb-none">
          <q-select
            option-label="NAMA_KELAS"
            :options="classes"
            autocomplete="NAMA_KELAS"
            dense
            outlined
            v-model="newClassDepositData.class.value"
            label="Class"
            :rules="[(val) => !!val || 'Class is missing']"
          />
          <q-input
            dense
            outlined
            v-model="newClassDepositData.amount.value"
            :rules="[
              (val) => !!val || 'Amount is missing',
              (val) => val == 5 || val == 10 || 'Amount must be 5 or 10',
            ]"
            label="Amount"
          />
        </q-card-section>
        <q-card-section class="q-py-none">
          This transaction will get bonus
          {{
            parseInt(newClassDepositData.amount.value) >=
            promos.filter((p) => p.JENIS_PROMO == 1)[0].MINIMAL_TRANSAKSI_PROMO
              ? promos.filter((p) => p.JENIS_PROMO == 1)[0]
                  .BONUS_TRANSAKSI_PROMO
              : 0
          }}
          class
          <br />
          <template
            v-if="
              newClassDepositData.class.value != null &&
              newClassDepositData.amount.value.length > 0
            "
          >
            The fare is Rp
            {{
              classes.filter(
                (c) => c.ID_KELAS == newClassDepositData.class.value.ID_KELAS
              )[0].TARIF_KELAS * parseInt(newClassDepositData.amount.value)
            }}
          </template>
        </q-card-section>
        <q-card-section>
          <div class="row justify-end">
            <q-btn type="submit" color="primary" label="Save" />
          </div>
        </q-card-section>
      </q-form>
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
import { nanoid } from 'nanoid';
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
export default defineComponent({
  name: 'MembersComponent',
  setup() {
    const $q = useQuasar();
    const columns = [
      {
        name: 'id',
        required: false,
        label: 'ID',
        align: 'left',
        field: (row) => row.ID_MEMBER,
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
        name: 'address',
        required: false,
        label: 'Address',
        align: 'left',
        field: (row) => row.ALAMAT_MEMBER,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'phone',
        required: false,
        label: 'Phone',
        align: 'left',
        field: (row) => row.TELEPON_MEMBER,
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
      address: ref(''),
      phone: ref(''),
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
      address: ref(''),
      phone: ref(''),
    };
    const newDataPhotoHover = ref(false);
    const searchString = ref('');
    const memberCardDialog = ref(false);
    const memberCardDialogSpinnerState = ref(true);
    const memberCardOpenedCanvas = ref(null);
    const memberCardOpenedMemberID = ref('');
    const transactionsTab = ref('activations');
    const transactionDialog = ref(false);
    const selectedMemberID = ref(null);
    const newMoneyDepositData = {
      idUser: ref(''),
      idMember: ref(''),
      cash: ref(''),
    };
    const newMoneyDepositDialog = ref(false);
    const promos = ref([]);
    const classes = ref([]);
    const newClassDepositDialog = ref(false);
    const newClassDepositData = {
      idUser: ref(''),
      idMember: ref(''),
      class: ref(null),
      amount: ref(''),
      cash: ref(''),
    };
    async function fetchPromos() {
      promos.value = [];
      return api
        .get('/promo', {
          withCredentials: true,
        })
        .then((response) => {
          promos.value = response.data;
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while fetching promos',
            color: 'negative',
            position: 'top-right',
          });
        });
    }
    async function fetchMembers() {
      rows.value = [];
      return api
        .get('/member', {
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
        .get('/member/generateID', {
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
      editData.id.value = user.ID_MEMBER;
      editData.photo.value = user.FOTO_USER;
      editData.name.value = user.NAMA_USER;
      editData.email.value = user.EMAIL_USER;
      editData.dateOfBirth.value = user.TANGGAL_LAHIR_USER.substring(0, 10)
        .replace('-', '/')
        .replace('-', '/');
      editData.address.value = user.ALAMAT_MEMBER;
      editData.phone.value = user.TELEPON_MEMBER;
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
      editData.address.value = '';
      editData.phone.value = '';
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
      newData.address.value = '';
      newData.phone.value = '';
    }
    function deleteData(id_member, id_user) {
      api
        .post(
          '/member/delete',
          {
            ID_MEMBER: id_member,
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
          fetchMembers();
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
        deleteData(user.ID_MEMBER, user.ID_USER);
      });
    }
    function saveEditData() {
      api
        .post(
          '/member/update',
          {
            ID_USER: editData.id_user.value,
            ID_MEMBER: editData.id.value,
            NAMA_USER: editData.name.value,
            EMAIL_USER: editData.email.value,
            TANGGAL_LAHIR_USER: editData.dateOfBirth.value,
            ALAMAT_MEMBER: editData.address.value,
            TELEPON_MEMBER: editData.phone.value,
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
          fetchMembers();
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
          '/member/create',
          {
            ID_USER: newData.id_user.value,
            ID_MEMBER: newData.id.value,
            NAMA_USER: newData.name.value,
            EMAIL_USER: newData.email.value,
            TANGGAL_LAHIR_USER: newData.dateOfBirth.value,
            ALAMAT_MEMBER: newData.address.value,
            TELEPON_MEMBER: newData.phone.value,
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
          fetchMembers();
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
    function displayMemberCard(member) {
      memberCardDialog.value = true;
      memberCardOpenedMemberID.value = member.ID_MEMBER;
      var background = new Image();
      background.onload = () => {
        if (memberCardDialog.value) {
          var canvas = document.createElement('canvas');
          canvas.width = 428;
          canvas.height = 270;
          document.getElementById('memberCardDisplay').appendChild(canvas);
          var ctx = canvas.getContext('2d');
          ctx.globalCompositeOperation = 'darken';
          ctx.drawImage(background, 0, 0);
          ctx.font = '50px Arial';
          ctx.fillText(
            member.NAMA_USER.length > 15
              ? member.NAMA_USER.substring(0, 12) + '...'
              : member.NAMA_USER,
            25,
            140
          );
          ctx.font = '18px Courier New';
          ctx.fillText('MEMBER ID: ' + member.ID_MEMBER, 25, 200);
          ctx.fillText(
            'ADDRESS  : ' +
              (member.ALAMAT_MEMBER.length > 23
                ? member.ALAMAT_MEMBER.substring(0, 21) + '...'
                : member.ALAMAT_MEMBER),
            25,
            220
          );
          ctx.fillText('PHONE    : ' + member.TELEPON_MEMBER, 25, 240);
          memberCardDialogSpinnerState.value = false;
          memberCardOpenedCanvas.value = canvas;
        }
      };
      background.src = 'src/assets/membercard.jpg';
    }
    function closeMemberCard() {
      memberCardDialog.value = false;
      memberCardDialogSpinnerState.value = true;
      memberCardOpenedMemberID.value = '';
    }
    function printMemberCard() {
      var memberCardPrintWindow = window.open(
        '',
        'PRINT',
        'height=500,width=1000'
      );
      memberCardPrintWindow.document.write(
        '<img src="' + memberCardOpenedCanvas.value.toDataURL() + '">'
      );
      memberCardPrintWindow.print();
      memberCardPrintWindow.close();
    }
    function recoverPasswordConfirm(member) {
      $q.dialog({
        title: 'Confirm',
        message:
          'Are you sure you want to recover password for ' +
          member.NAMA_USER +
          '? This action cannot be undone.',
        cancel: true,
        persistent: true,
        ok: {
          color: 'primary',
          label: 'Recover',
        },
      }).onOk(() => {
        api
          .post(
            '/autentikasi/recoverPassword',
            {
              ID_USER: member.ID_USER,
            },
            { withCredentials: true }
          )
          .then(() => {
            $q.notify({
              message: 'Success recovering password',
              color: 'positive',
              icon: 'check_circle',
              position: 'top-right',
            });
            fetchMembers();
          })
          .catch(() => {
            $q.notify({
              message: 'An error occurred while recovering password',
              color: 'negative',
              icon: 'cancel',
              position: 'top-right',
            });
          });
      });
    }
    function showTransactionDialog(member) {
      transactionDialog.value = true;
      selectedMemberID.value = member.ID_MEMBER;
    }
    function printActivationReceipt(id, membershipId) {
      var activationReceiptBackground = new Image();
      activationReceiptBackground.onload = () => {
        let receiptWindow = window.open('', 'PRINT', 'height=500,width=1000');
        let canvas = receiptWindow.document.createElement('canvas');
        canvas.width = 903;
        canvas.height = 270;
        receiptWindow.document.body.appendChild(canvas);
        let ctx = canvas.getContext('2d');
        ctx.globalCompositeOperation = 'darken';
        ctx.drawImage(activationReceiptBackground, 0, 0);
        ctx.font = '50px Arial';
        ctx.fillText('Activation Transaction', 25, 140);
        ctx.font = '18px Courier New';
        ctx.fillText(
          'RECEIPT ID: ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MEMBERSHIP_TRANSACTIONS.filter(
                (mTransaction) => mTransaction.ID_MEMBERSHIP == membershipId
              )[0].ID_MEMBERSHIP,
          540,
          200
        );
        ctx.fillText(
          'DATE      : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MEMBERSHIP_TRANSACTIONS.filter(
                (mTransaction) => mTransaction.ID_MEMBERSHIP == membershipId
              )[0].TANGGAL_AKTIVASI_MEMBERSHIP,
          540,
          220
        );
        ctx.fillText(
          'MEMBER    : ' +
            rows.value.filter((row) => row.ID_MEMBER == id)[0].ID_MEMBER +
            '/' +
            rows.value.filter((row) => row.ID_MEMBER == id)[0].NAMA_USER,
          25,
          200
        );
        ctx.fillText('CASH      : Rp 3.000.000,-', 25, 220);
        ctx.fillText(
          'EXPIRED ON: ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MEMBERSHIP_TRANSACTIONS.filter(
                (mTransaction) => mTransaction.ID_MEMBERSHIP == membershipId
              )[0].TANGGAL_KADALUARSA_MEMBERSHIP,
          25,
          240
        );
        ctx.fillText(
          'CASHIER   : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MEMBERSHIP_TRANSACTIONS.filter(
                (mTransaction) => mTransaction.ID_MEMBERSHIP == membershipId
              )[0].ID_PEGAWAI +
            '/' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MEMBERSHIP_TRANSACTIONS.filter(
                (mTransaction) => mTransaction.ID_MEMBERSHIP == membershipId
              )[0].NAMA_USER,
          540,
          240
        );
        receiptWindow.print();
        receiptWindow.close();
      };
      activationReceiptBackground.src = 'src/assets/receipt.jpg';
    }
    function showNewMoneyDepositDialog(user) {
      newMoneyDepositDialog.value = true;
      newMoneyDepositData.idUser.value = user.ID_USER;
      newMoneyDepositData.idMember.value = user.ID_MEMBER;
      newMoneyDepositData.cash.value = '';
    }
    function saveMoneyDepositData() {
      newMoneyDepositDialog.value = false;
      api
        .post(
          '/depositUang/create',
          {
            MEM_ID_USER: newMoneyDepositData.idUser.value,
            ID_MEMBER: newMoneyDepositData.idMember.value,
            JUMLAH_DEPOSIT_UANG: newMoneyDepositData.cash.value,
            PEG_ID_USER: JSON.parse(sessionStorage.getItem('user')).ID_USER,
            ID_PEGAWAI: JSON.parse(sessionStorage.getItem('user')).ID_PEGAWAI,
            BONUS_DEPOSIT_UANG:
              parseInt(newMoneyDepositData.cash.value) >=
              promos.value.filter((p) => p.JENIS_PROMO == 0)[0]
                .MINIMAL_TRANSAKSI_PROMO
                ? promos.value.filter((p) => p.JENIS_PROMO == 0)[0]
                    .BONUS_TRANSAKSI_PROMO
                : 0,
          },
          { withCredentials: true }
        )
        .then(() => {
          $q.notify({
            message: 'Success creating new money deposit',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right',
          });
          fetchMembers();
        });
    }
    function printMoneyDepositReceipt(id, moneyDepositId) {
      var moneyDepositReceiptBackground = new Image();
      moneyDepositReceiptBackground.onload = () => {
        let receiptWindow = window.open('', 'PRINT', 'height=500,width=1000');
        let canvas = receiptWindow.document.createElement('canvas');
        canvas.width = 903;
        canvas.height = 270;
        receiptWindow.document.body.appendChild(canvas);
        let ctx = canvas.getContext('2d');
        ctx.globalCompositeOperation = 'darken';
        ctx.drawImage(moneyDepositReceiptBackground, 0, 0);
        ctx.font = '50px Arial';
        ctx.fillText('Money Deposit Transaction', 25, 140);
        ctx.font = '18px Courier New';
        ctx.fillText(
          'RECEIPT ID: ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MONEY_DEPOSIT_TRANSACTIONS.filter(
                (mdTransaction) =>
                  mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
              )[0].NO_STRUK_DEPOSIT_UANG,
          540,
          200
        );
        ctx.fillText(
          'DATE      : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MONEY_DEPOSIT_TRANSACTIONS.filter(
                (mdTransaction) =>
                  mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
              )[0].TANGGAL_DEPOSIT_UANG,
          540,
          220
        );
        ctx.fillText(
          'MEMBER    : ' +
            rows.value.filter((row) => row.ID_MEMBER == id)[0].ID_MEMBER +
            '/' +
            rows.value.filter((row) => row.ID_MEMBER == id)[0].NAMA_USER,
          25,
          180
        );
        ctx.fillText(
          'CASH      : Rp ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MONEY_DEPOSIT_TRANSACTIONS.filter(
                (mdTransaction) =>
                  mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
              )[0].JUMLAH_DEPOSIT_UANG,
          25,
          200
        );
        ctx.fillText(
          'BONUS     : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MONEY_DEPOSIT_TRANSACTIONS.filter(
                (mdTransaction) =>
                  mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
              )[0].BONUS_DEPOSIT_UANG,
          25,
          220
        );
        ctx.fillText(
          'REMAINING : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MONEY_DEPOSIT_TRANSACTIONS.filter(
                (mdTransaction) =>
                  mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
              )[0].SISA_DEPOSIT_UANG,
          25,
          240
        );
        ctx.fillText(
          'TOTAL     : Rp ' +
            (parseInt(
              rows.value
                .filter((row) => row.ID_MEMBER == id)[0]
                .MONEY_DEPOSIT_TRANSACTIONS.filter(
                  (mdTransaction) =>
                    mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
                )[0].BONUS_DEPOSIT_UANG
            ) +
              parseInt(
                rows.value
                  .filter((row) => row.ID_MEMBER == id)[0]
                  .MONEY_DEPOSIT_TRANSACTIONS.filter(
                    (mdTransaction) =>
                      mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
                  )[0].SISA_DEPOSIT_UANG
              ) +
              parseInt(
                rows.value
                  .filter((row) => row.ID_MEMBER == id)[0]
                  .MONEY_DEPOSIT_TRANSACTIONS.filter(
                    (mdTransaction) =>
                      mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
                  )[0].JUMLAH_DEPOSIT_UANG
              )),
          540,
          180
        );
        ctx.fillText(
          'CASHIER   : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MONEY_DEPOSIT_TRANSACTIONS.filter(
                (mdTransaction) =>
                  mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
              )[0].ID_PEGAWAI +
            '/' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .MONEY_DEPOSIT_TRANSACTIONS.filter(
                (mdTransaction) =>
                  mdTransaction.NO_STRUK_DEPOSIT_UANG == moneyDepositId
              )[0].NAMA_USER,
          540,
          240
        );
        receiptWindow.print();
        receiptWindow.close();
      };
      moneyDepositReceiptBackground.src = 'src/assets/receipt.jpg';
    }
    function showNewClassDepositDialog(user) {
      newClassDepositDialog.value = true;
      newClassDepositData.idUser.value = user.ID_USER;
      newClassDepositData.idMember.value = user.ID_MEMBER;
      newClassDepositData.cash.value = '';
      newClassDepositData.amount.value = '';
      newClassDepositData.class.value = null;
    }
    function saveClassDepositData() {
      newClassDepositDialog.value = false;
      api
        .post(
          '/depositKelas/create',
          {
            ID_KELAS: newClassDepositData.class.value.ID_KELAS,
            MEM_ID_USER: newClassDepositData.idUser.value,
            ID_MEMBER: newClassDepositData.idMember.value,
            JUMLAH_DEPOSIT_KELAS: newClassDepositData.amount.value,
            PEG_ID_USER: JSON.parse(sessionStorage.getItem('user')).ID_USER,
            ID_PEGAWAI: JSON.parse(sessionStorage.getItem('user')).ID_PEGAWAI,
            JUMLAH_BAYAR_DEPOSIT_KELAS:
              classes.value.filter(
                (c) => c.ID_KELAS == newClassDepositData.class.value.ID_KELAS
              )[0].TARIF_KELAS * parseInt(newClassDepositData.amount.value),
            BONUS_DEPOSIT_KELAS:
              parseInt(newClassDepositData.amount.value) >=
              promos.value.filter((p) => p.JENIS_PROMO == 1)[0]
                .MINIMAL_TRANSAKSI_PROMO
                ? promos.value.filter((p) => p.JENIS_PROMO == 1)[0]
                    .BONUS_TRANSAKSI_PROMO
                : 0,
          },
          { withCredentials: true }
        )
        .then(() => {
          $q.notify({
            message: 'Success creating new deposit',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right',
          });
          fetchMembers();
        });
    }
    function printClassDepositReceipt(id, classDepositId) {
      var classDepositReceiptBackground = new Image();
      classDepositReceiptBackground.onload = () => {
        let receiptWindow = window.open('', 'PRINT', 'height=500,width=1000');
        let canvas = receiptWindow.document.createElement('canvas');
        canvas.width = 903;
        canvas.height = 270;
        receiptWindow.document.body.appendChild(canvas);
        let ctx = canvas.getContext('2d');
        ctx.globalCompositeOperation = 'darken';
        ctx.drawImage(classDepositReceiptBackground, 0, 0);
        ctx.font = '50px Arial';
        ctx.fillText('Class Deposit Transaction', 25, 140);
        ctx.font = '18px Courier New';
        ctx.fillText(
          'RECEIPT ID: ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .CLASS_DEPOSIT_TRANSACTIONS.filter(
                (cdTransaction) =>
                  cdTransaction.NO_STRUK_DEPOSIT_KELAS == classDepositId
              )[0].NO_STRUK_DEPOSIT_KELAS,
          540,
          200
        );
        ctx.fillText(
          'DATE      : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .CLASS_DEPOSIT_TRANSACTIONS.filter(
                (cdTransaction) =>
                  cdTransaction.NO_STRUK_DEPOSIT_KELAS == classDepositId
              )[0].TANGGAL_DEPOSIT_KELAS,
          540,
          220
        );
        ctx.fillText(
          'MEMBER    : ' +
            rows.value.filter((row) => row.ID_MEMBER == id)[0].ID_MEMBER +
            '/' +
            rows.value.filter((row) => row.ID_MEMBER == id)[0].NAMA_USER,
          25,
          180
        );
        ctx.fillText(
          'CASH      : Rp ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .CLASS_DEPOSIT_TRANSACTIONS.filter(
                (cdTransaction) =>
                  cdTransaction.NO_STRUK_DEPOSIT_KELAS == classDepositId
              )[0].JUMLAH_BAYAR_DEPOSIT_KELAS,
          25,
          200
        );
        ctx.fillText(
          'AMOUNT    : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .CLASS_DEPOSIT_TRANSACTIONS.filter(
                (cdTransaction) =>
                  cdTransaction.NO_STRUK_DEPOSIT_KELAS == classDepositId
              )[0].JUMLAH_DEPOSIT_KELAS,
          25,
          220
        );
        ctx.fillText(
          'BONUS     : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .CLASS_DEPOSIT_TRANSACTIONS.filter(
                (cdTransaction) =>
                  cdTransaction.NO_STRUK_DEPOSIT_KELAS == classDepositId
              )[0].BONUS_DEPOSIT_KELAS,
          25,
          240
        );
        ctx.fillText(
          'CLASS     : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .CLASS_DEPOSIT_TRANSACTIONS.filter(
                (cdTransaction) =>
                  cdTransaction.NO_STRUK_DEPOSIT_KELAS == classDepositId
              )[0].NAMA_KELAS,
          540,
          180
        );
        ctx.fillText(
          'CASHIER   : ' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .CLASS_DEPOSIT_TRANSACTIONS.filter(
                (cdTransaction) =>
                  cdTransaction.NO_STRUK_DEPOSIT_KELAS == classDepositId
              )[0].ID_PEGAWAI +
            '/' +
            rows.value
              .filter((row) => row.ID_MEMBER == id)[0]
              .CLASS_DEPOSIT_TRANSACTIONS.filter(
                (cdTransaction) =>
                  cdTransaction.NO_STRUK_DEPOSIT_KELAS == classDepositId
              )[0].NAMA_USER,
          540,
          240
        );
        receiptWindow.print();
        receiptWindow.close();
      };
      classDepositReceiptBackground.src = 'src/assets/receipt.jpg';
    }
    onMounted(() => {
      fetchMembers();
      fetchPromos();
      api
        .get('/kelas/', {
          withCredentials: true,
        })
        .then((response) => {
          classes.value = response.data;
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while getting list of classes',
            color: 'negative',
            position: 'top-right',
          });
        });
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
      memberCardDialog,
      displayMemberCard,
      closeMemberCard,
      memberCardDialogSpinnerState,
      printMemberCard,
      memberCardOpenedCanvas,
      memberCardOpenedMemberID,
      recoverPasswordConfirm,
      fetchMembers,
      transactionDialog,
      transactionsTab,
      showTransactionDialog,
      selectedMemberID,
      nanoid,
      api,
      sessionStorage,
      printActivationReceipt,
      newMoneyDepositData,
      newMoneyDepositDialog,
      showNewMoneyDepositDialog,
      promos,
      saveMoneyDepositData,
      classes,
      newClassDepositDialog,
      newClassDepositData,
      showNewClassDepositDialog,
      saveClassDepositData,
      printMoneyDepositReceipt,
      printClassDepositReceipt,
    };
  },
});
</script>
