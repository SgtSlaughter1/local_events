<template>
    <div class="landing-page">
        <!-- Hero Section -->
        <section class="hero text-center py-5">
            <div class="container">
                <div class="row min-vh-75 align-items-center">
                    <div class="col-lg-8 mx-auto">
                        <h1 class="display-1 fw-bold mb-4">Welcome to Event Management</h1>
                        <p class="lead fs-2 mb-5">Your one-stop platform for managing and attending events</p>
                        <div class="cta-buttons">
                            <button class="btn btn-primary btn-lg me-3 px-5 py-3" @click="navigateToEvents">Browse Events</button>
                            <button class="btn btn-outline-primary btn-lg px-5 py-3" @click="navigateToRegister">Register Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

                <!-- Categories Section -->
                <section class="categories py-5">
            <div class="container">
                <h2 class="text-center mb-4">Explore Categories</h2>
                <div class="row g-4">
                    <div v-for="category in categories" :key="category.name" class="col-md-4 col-lg-2">
                        <div class="category-card text-center">
                            <img :src="`/images/${category.image}`" :alt="category.name" class="img-fluid rounded-circle mb-3" />
                            <h5>{{ category.name }}</h5>
                    </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-4">Why Choose Us?</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card text-center p-4">
                            <i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
                    <h3>Easy Event Management</h3>
                    <p>Create and manage your events with our intuitive platform</p>
                </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card text-center p-4">
                            <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h3>Seamless Registration</h3>
                    <p>Simple and quick registration process for attendees</p>
                </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card text-center p-4">
                            <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                    <h3>Real-time Analytics</h3>
                    <p>Track your event's performance with detailed analytics</p>
                </div>
            </div>
                </div>
            </div>
        </section>

        <!-- Events Section -->
        <section class="events py-5 bg-light">
            <div class="container">
                <!-- Upcoming Events -->
                <div class="mb-5">
                    <h2 class="text-center mb-4">Upcoming Events</h2>
                    <div class="row g-4">
                        <div v-for="event in upcomingEvents" :key="event.id" class="col-md-6 col-lg-4">
                            <div class="event-card card h-100">
                                <img :src="event.image_url || '/images/tech-conference.jpg'" :alt="event.title" class="card-img-top" />
                                <div class="card-body">
                                    <h5 class="card-title">{{ event.title }}</h5>
                                    <p class="card-text">
                                        <i class="fas fa-calendar me-2"></i>{{ formatDate(event.start_date) }}<br>
                                        <i class="fas fa-map-marker-alt me-2"></i>{{ event.location }}
                                    </p>
                                    <button class="btn btn-outline-primary" @click="viewEvent(event.id)">Learn More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Online Events -->
                <div>
                    <h2 class="text-center mb-4">Discover Best of Online Events</h2>
                    <div class="row g-4">
                        <div v-for="event in onlineEvents" :key="event.id" class="col-md-6 col-lg-4">
                            <div class="event-card card h-100">
                                <img :src="event.image" :alt="event.title" class="card-img-top" />
                                <div class="card-body">
                                    <h5 class="card-title">{{ event.title }}</h5>
                                    <p class="card-text">
                                        <i class="fas fa-calendar me-2"></i>{{ formatDate(event.date) }}
                                    </p>
                                    <button class="btn btn-outline-primary">See More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta py-5 text-center">
            <div class="container">
                <h3 class="fw-bold mb-3">Events specially curated for you!</h3>
                <p class="lead mb-4">Event suggestions exclusively picked for your interests</p>
                <button class="btn btn-primary btn-lg">Get Started</button>
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
            
            categories: [
                { name: "Entertainment", image: "entertainment.png" },
                { name: "Culture & Arts", image: "culture.png" },
                { name: "Sports & Fitness", image: "sport.png" },
                { name: "Educational & Business", image: "education.png" },
                { name: "Technology", image: "tech.png" },
                { name: "Travel & Adventure", image: "travel.png" },
            ],
            onlineEvents: [
                {
                    id: 1,
                    title: 'LinkedIn Webinar',
                    date: '2024-06-01T15:00:00',
                    image: '/images/webinar.jpg',
                },
                {
                    id: 2,
                    title: 'HackerX Virtual Meetup',
                    date: '2024-06-05T18:00:00',
                    image: '/images/hackerx.jpg',
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
