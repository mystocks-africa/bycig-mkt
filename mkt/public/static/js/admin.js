function handleUpdateStatus(postId, clusterLeaderEmail, status) {
    if (typeof postId !== 'number' || postId <= 0) {
        alert("Invalid post ID.");
        return;
    }

    if (typeof clusterLeaderEmail !== 'string' || clusterLeaderEmail.trim() === '') {
        alert("Invalid cluster leader email.");
        return;
    }

    if (typeof status !== 'string' || !['accept', 'decline'].includes(status)) {
        alert("Invalid status. Must be 'accept' or 'decline'.");
        return;
    }

    const params = new URLSearchParams({
        post_id: postId,
        cluster_leader_email: clusterLeaderEmail,
        status: status
    });

    fetch(`/admin/handle-proposal-status?${params.toString()}`, {
        method: 'PUT',
    })
    .catch(error => {
        window.location.href = `/redirect?message=${encodeURIComponent(error.message)}&message_type=error`;
    });
}
