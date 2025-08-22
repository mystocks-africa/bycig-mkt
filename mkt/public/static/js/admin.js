// HTML form does not support DELETE or PUT methods directly.
function handleDeleteProposal(postId) {
    if (typeof postId !== 'number' || postId <= 0) {
        alert("Invalid post ID.");
        return;
    }
    
    if (confirm("Are you sure you want to delete this proposal? This action cannot be undone.")) {
        fetch(`/admin/delete-proposal?post_id=${postId}`, {
            method: 'DELETE',
        })
        .then(() => {
            window.location.href = '/redirect?message=Proposal deleted successfully&message_type=success';
        })
        .catch((error) => {
            window.location.href = `/redirect?message=${encodeURIComponent(error.message)}&message_type=error`;
        });
    }
}

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
    .then(() => {
        window.location.href = '/redirect?message=Proposal deleted successfully&message_type=success';
    })
    .catch(error => {
        window.location.href = `/redirect?message=${encodeURIComponent(error.message)}&message_type=error`;
    });
}
