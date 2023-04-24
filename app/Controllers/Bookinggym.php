<?php

namespace App\Controllers;

class Bookinggym extends BaseController
{
    public function getIndex()
    {
        return $this->respond($this->db->query(
            'SELECT * FROM booking_gyms'
            )->getResultArray(), 200);
    }
    public function postCreate()
    {
        $data = $this->request->getPost();
        $this->db->query(
            'INSERT INTO booking_gyms (
                ID_BOOKING_GYM,
                ID_USER,
                ID_MEMBER,
                TANGGAL_BOOKING_GYM,
                SESI_BOOKING_GYM)
            VALUES ("'
                . base64_encode(random_bytes(24)) . '", "'
                . $data['ID_USER'] . '", "'
                . $data['ID_MEMBER'] . '", "'
                . $data['TANGGAL_BOOKING_GYM'] . '", "'
                . $data['SESI_BOOKING_GYM']
                . '");'
            );
        return $this->respondCreated($data);
    }
    public function postDelete()
    {
        $data = $this->request->getPost();
        $this->db->query(
            'UPDATE booking_gyms
            SET IS_DELETED_BOOKING_GYM = 1
            WHERE ID_BOOKING_GYM = "' . $data['ID_BOOKING_GYM'] . '";'
        );
        return $this->respondDeleted($data);
    }
}