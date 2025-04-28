<template>
    <div class="landing-page">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Event Management</h1>
                <p class="subtitle">Your one-stop platform for managing and attending events</p>
                <div class="cta-buttons">
                    <button class="btn primary" @click="navigateToEvents">Browse Events</button>
                    <button class="btn secondary" @click="navigateToRegister">Register Now</button>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <h2>Why Choose Us?</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Easy Event Management</h3>
                    <p>Create and manage your events with our intuitive platform</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-users"></i>
                    <h3>Seamless Registration</h3>
                    <p>Simple and quick registration process for attendees</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Real-time Analytics</h3>
                    <p>Track your event's performance with detailed analytics</p>
                </div>
            </div>
        </section>

        <!-- Upcoming Events Section -->
        <section class="upcoming-events">
            <h2>Upcoming Events</h2>
            <div class="events-grid">
                <!-- Event cards will be dynamically populated -->
                <div v-for="event in upcomingEvents" :key="event.id" class="event-card">
                    <div class="event-image">
                        <img :src="event.image" :alt="event.title" />
                    </div>
                    <div class="event-details">
                        <h3>{{ event.title }}</h3>
                        <p class="event-date">{{ formatDate(event.start_date) }}</p>
                        <p class="event-location">{{ event.location }}</p>
                        <button class="btn secondary" @click="viewEvent(event.id)">Learn More</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: 'LandingPage',
    data() {
        return {
            upcomingEvents: [
                {
                    id: 1,
                    title: 'Tech Conference 2024',
                    start_date: '2024-04-15T09:00:00',
                    location: 'San Francisco, CA',
                    image: '/images/tech-conference.jpg',
                },
                {
                    id: 2,
                    title: 'Business Networking Event',
                    start_date: '2024-04-20T18:00:00',
                    location: 'New York, NY',
                    image: '/images/networking.jpg',
                },
                {
                    id: 3,
                    title: 'Workshop: Digital Marketing',
                    start_date: '2024-04-25T10:00:00',
                    location: 'Chicago, IL',
                    image: '/images/workshop.jpg',
                },
            ],
        }
    },
    methods: {
        navigateToEvents() {
            this.$router.push('/events')
        },
        navigateToRegister() {
            this.$router.push('/register')
        },
        viewEvent(eventId) {
            this.$router.push(`/events/${eventId}`)
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            })
        },
    },
}
</script>
