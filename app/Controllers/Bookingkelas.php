<?php

namespace App\Controllers;

class Bookingkelas extends BaseController
{
    public function getIndex()
    {
        return $this->respond($this->db->query(
            'SELECT * FROM booking_kelass'
            )->getResultArray(), 200);
    }
    public function postCreate()
    {
        $data = $this->request->getPost();
        $this->db->query(
            'INSERT INTO booking_gyms (
                ID_BOOKING_KELAS,
                ID_JADWAL,
                ID_USER,
                ID_MEMBER,
                TANGGAL_BOOKING_KELAS)
            VALUES ("'
                . base64_encode(random_bytes(24)) . '", "'
                . $data['ID_JADWAL'] . '", "'
                . $data['ID_USER'] . '", "'
                . $data['ID_MEMBER'] . '", "'
                . $data['TANGGAL_BOOKING_KELAS']
                . '");'
            );
        return $this->respondCreated($data);
    }
    public function postDelete()
    {
        $data = $this->request->getPost();
        $this->db->query(
            'UPDATE booking_kelass
            SET IS_DELETED_BOOKING_KELAS = 1
            WHERE ID_BOOKING_KELAS = "' . $data['ID_BOOKING_KELAS'] . '";'
        );
        return $this->respondDeleted($data);
    }
}