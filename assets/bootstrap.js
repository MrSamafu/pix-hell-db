import { Application } from '@hotwired/stimulus';

const app = Application.start();

// Configure Stimulus development experience
app.debug = false;
window.Stimulus = app;

export { app };