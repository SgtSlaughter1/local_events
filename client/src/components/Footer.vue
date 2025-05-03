<template>
  <footer class="bg-dark text-light py-4 mt-auto">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <h5>Event Management</h5>
          <p class="text-muted">Your one-stop platform for managing and attending events.</p>
        </div>
        <div class="col-md-4">
          <h5>Quick Links</h5>
          <ul class="list-unstyled">
            <li><router-link to="/" class="text-muted text-decoration-none">Home</router-link></li>
            <li>
              <router-link to="/events" class="text-muted text-decoration-none">Events</router-link>
            </li>
            <li>
              <router-link to="/about" class="text-muted text-decoration-none">About</router-link>
            </li>
            <li>
              <router-link to="/contact" class="text-muted text-decoration-none"
                >Contact</router-link
              >
            </li>
          </ul>
        </div>
        <div class="col-md-4">
          <h5>Contact Us</h5>
          <ul class="list-unstyled text-muted">
            <li><i class="fas fa-envelope me-2"></i> info@eventmanagement.com</li>
            <li><i class="fas fa-phone me-2"></i> +1 (555) 123-4567</li>
            <li><i class="fas fa-map-marker-alt me-2"></i> 123 Event Street, City, Country</li>
          </ul>
        </div>
      </div>
      <hr class="my-4" />
      <div class="row">
        <div class="col-md-6">
          <p class="mb-0">&copy; 2025 Event Management. All rights reserved.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <a href="#" class="text-muted me-3"><i class="fab fa-facebook"></i></a>
          <a href="#" class="text-muted me-3"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-muted me-3"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-muted"><i class="fab fa-linkedin"></i></a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer',
}
</script>
