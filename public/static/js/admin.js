// HTML form does not support DELETE or PUT methods directly.

function handleDeleteProposal(postId) {
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
    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('cluster_leader_email', clusterLeaderEmail);
    formData.append('status', status);

    fetch('/admin/update-proposal-status', {
        method: 'PUT',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Proposal status updated successfully.');
            location.reload();
        } else {
            alert('Error updating proposal status: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        alert('An error occurred while updating the proposal status.');
    });
}