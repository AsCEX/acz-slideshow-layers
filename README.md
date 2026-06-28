# ACZ Slideshow Layers

ACZ Slideshow Layers is a WordPress plugin that adds an Elementor widget named **Layer Slideshow**. Each slide is a nested Elementor container, so you can drop normal Elementor widgets inside every slide layer.

## Requirements

- WordPress
- Elementor
- Elementor Nested Elements support enabled

## Installation

1. Place this folder in `wp-content/plugins/acz-slideshow-layers`.
2. Activate **ACZ Slideshow Layers** in WordPress admin.
3. Edit a page with Elementor.
4. Search for **Layer Slideshow** under **ACZ Elements**.

## Usage

1. Drag **Layer Slideshow** into your Elementor layout.
2. In the **Slides** section, add, remove, rename, or reorder slides.
3. Open Elementor Structure/Navigator to edit each slide container.
4. Add Elementor widgets inside each slide container.
5. Configure slider behavior and styles from the widget panel.

New widgets create default nested slide containers named **Slide #1**, **Slide #2**, and **Slide #3**.

## Controls

### Slides

- Slide title
- Optional CSS ID
- Add/remove/reorder slides

### Slider Settings

- Autoplay
- Autoplay speed
- Transition speed
- Loop
- Pause on hover
- Show/hide arrows
- Show/hide dots
- Minimum height

### Style: Slide Area

- Background
- Border
- Box shadow
- Border radius

### Style: Arrows

- Previous icon
- Next icon
- Normal color/background
- Hover color/background
- Icon size

### Style: Dot Pagination

- Position
- Edge offset
- Gap
- Normal dot color
- Active dot color

## Frontend Features

- Arrow navigation
- Dot pagination
- Swipe/drag navigation
- Autoplay with pause on hover
- Elementor editor preview support

## Troubleshooting

If changes do not appear in Elementor, hard refresh the editor so updated CSS/JS assets load.

If nested slide containers do not appear, delete the old widget instance and drag in a fresh **Layer Slideshow** widget. Elementor creates nested child containers when the widget is first inserted.

If the plugin shows a missing requirement notice, make sure Elementor is active and the installed Elementor version supports Nested Elements.

## Version

Current plugin version: `1.0.10`
