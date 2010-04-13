<?php


class Message_model extends Model {

    function get_messages($id, $type_id, $per_page, $nr)
    {
        $field = "receiver_id";
        if ($type_id == "2")
            $field = "sender_id";
        $this->db->select('m.subject,
                             m.date_sent,
                             m.content,
                             us.username as \'sendername\',
                             ur.username as \'receivername\'');
        $this->db->join('message m', 'm.id = mr.message_id');
        $this->db->join('user us','us.id = mr.sender_id');
        $this->db->join('user ur','ur.id = mr.receiver_id');
        $this->db->where($field, $id);
//        $this->db->where('type_id', $type_id);
        $query = $this->db->get('message_relation mr', $per_page, $nr);
        return $query;
    }

    function save_message($sender_id, $receiver_name, $content, $subject)
    {
        $this->db->select('id');
        $this->db->where('username', $receiver_name);
        $user = $this->db->get('user', 1);
        print_r($user);
        if ($user->num_rows() < 0 )
        {
            $receiver_id = $user->row()->id;
            $date_sent = getdate();
            $date_sent = $date_sent['year'] . "-" 
                            . $date_sent["month"]
                            . "-" . $date_sent["mday"];
            print_r($date_sent);
            $m_data = array('date_sent' =>  $date_sent,
                            'subject' => $subject,
                            'content' => $content);
            $this->db->insert('message', $m_data);
            $m_query_id = $this->db->insert_id();
            $mr_data = array('message_id' => $m_query_id,
                             'sender_id' => $sender_id,
                             'receiver_id' => $receiver_id);

            return $this->db->insert('message_relation', $mr_data);
        }
        else
        {
            return FALSE;
        }
    }

}