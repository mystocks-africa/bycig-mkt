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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Proposal deleted successfully.');
                location.reload();
            } else {
                alert('Error deleting proposal: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An error occurred while deleting the proposal.');
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

    if (typeof status !== 'string' || !['approved', 'rejected'].includes(status)) {
        alert("Invalid status. Must be 'approved', 'rejected', or 'pending'.");
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
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server responded with ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Proposal status updated successfully.');
            location.reload();
        } else {
            alert('Error updating proposal status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Request failed:', error);
        alert('An error occurred while updating the proposal status.');
    });
}
