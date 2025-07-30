<?php
function get_session_variables ($delete_vars = false) {
    session_start();
    $cluster_leader_id = $_SESSION["cluster_leader_id"];
    $proposal_id = $_SESSION["proposal_id"];
    $auth_to_access = $_SESSION["auth_to_access"];

    if ($delete_vars) {
        $_SESSION = [];
        session_destroy();
        session_abort();
    } else {
        session_write_close();
    }

    return [
        "cluster_leader_id"=> $cluster_leader_id,
        "proposal_id"=> $proposal_id,
        "auth_to_access"=> $auth_to_access
    ];

}