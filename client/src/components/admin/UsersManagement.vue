<template>
  <div class="users-management">
    <div class="coming-soon-container">
      <i class="fas fa-users fa-4x mb-4 text-primary"></i>
      <h1>Users Management</h1>
      <p class="lead">This feature is coming soon!</p>
      <p>You'll be able to manage users, their roles, and permissions here.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UsersManagement'
}
</script>

<style scoped>
.users-management {
  padding: 2rem;
  min-height: calc(100vh - 60px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.coming-soon-container {
  text-align: center;
  padding: 3rem;
  background: var(--white-color);
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 600px;
  width: 100%;
}

.coming-soon-container i {
  color: var(--accent-color);
  margin-bottom: 1.5rem;
}

.coming-soon-container h1 {
  color: var(--primary-color);
  margin-bottom: 1rem;
  font-weight: 600;
}

.coming-soon-container .lead {
  color: var(--text-color);
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.coming-soon-container p {
  color: var(--text-color);
  opacity: 0.8;
}
</style> 