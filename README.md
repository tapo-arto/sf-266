📢 SafetyFlash System

Overview

SafetyFlash is a modern digital platform designed to create, manage, and publish safety communications across an organization.

⸻

🎯 Purpose

The SafetyFlash system is built to:
	•	Standardize safety communication across the organization
	•	Improve traceability and version control
	•	Enable structured incident → investigation workflows
	•	Support multilingual communication
	•	Centralize publishing and distribution

⸻

🧩 SafetyFlash Types

1. First Release

Used when:
	•	An incident has occurred
	•	There is an injury requiring treatment

2. Dangerous Situation

Used when:
	•	A near miss occurs
	•	Equipment damage or minor incidents without injuries

3. Investigation Report

Used when:
	•	Investigation is completed
	•	Actions and conclusions are defined

⚠️ Investigation reports are always a continuation of an existing SafetyFlash.

⸻

⚙️ Core Features

🆕 6-Step Creation Workflow
	1.	Type selection
	2.	Location and time
	3.	Content
	4.	Images
	5.	Layout (card distribution)
	6.	Preview & publish

⸻

🧱 Dynamic Card Layout System
	•	Supports 1-card and 2-card layouts
	•	Logic depends on:
	•	SafetyFlash type
	•	User selection
	•	“Force 2 cards” option available (Investigation only)

Rules:
	•	First Release → always 1 card
	•	Dangerous Situation → always 1 card
	•	Investigation Report → 1 or 2 cards

⚠️ UI and backend logic must remain synchronized.

⸻

🖼️ Image Management & Annotation System

Image Sources:
	•	Upload from device
	•	Image bank selection

Features:
	•	Add annotations (markers) on images
	•	Store annotation data separately from image
	•	Re-render annotations in preview and edit modes

Important Behavior:
	•	Image selection and annotation are separate states
	•	Editing requires rehydrating both:
	•	image source
	•	annotation layer

⚠️ Known sensitivity:
	•	If image editor modal is not opened → annotations may not reload correctly
	•	State handling must be consistent across:
	•	form
	•	preview
	•	edit

⸻

🌍 Multilingual System
	•	Language versions are created from an existing SafetyFlash
	•	Automatically inherits:
	•	content
	•	images
	•	structure

Special Logic:
	•	Language version:
	•	skips base selection step
	•	does NOT allow choosing a new base SafetyFlash
	•	Form must start directly from normal workflow (Location step)

⚠️ Common issue:
	•	Incorrect state causes empty view until manual selection is triggered

⸻

🔗 Investigation Workflow

Investigation Reports can be created:
	1.	From form (selecting existing SafetyFlash)
	2.	(Recommended future) directly from view page

Behavior:
	•	Links to original SafetyFlash via ID
	•	Copies relevant data
	•	Some fields require manual re-selection (e.g. worksite)

⸻

👁️ Preview System
	•	Real-time rendering before publishing
	•	Uses asynchronous processing
	•	Depends on process-based temporary data

⚠️ Known issue:
	•	Loader may hang if:
	•	process data incomplete
	•	async response fails

⸻

📧 Email Publishing
	•	Publishing triggers email distribution
	•	Recipients:
	•	partly database-driven
	•	partly hardcoded (needs refactor)

Current implementation example:

function sf_get_publish_target_emails(): array
{
    return ['safetyflash@tapojarvi.online'];
}

⚠️ Recommended:
	•	Move fully to database configuration

⸻

🗂️ Archive & Lifecycle

Lifecycle:

Created → Published → Archived

	•	Archive available via UI
	•	Archived SafetyFlashes remain accessible

⚠️ Note:
	•	Archiving does NOT clean file storage

⸻

🧠 Technical Architecture

Backend
	•	PHP-based modular structure
	•	Separation of:
	•	logic
	•	views
	•	Database-driven configuration

⸻

Frontend
	•	Multi-step form system
	•	JavaScript-driven state handling
	•	Asynchronous preview rendering
	•	Responsive UI (desktop + mobile)

⸻

📁 Key Directories

/uploads/
	•	Stores images and generated assets

/uploads/processes/
	•	Temporary uploaded image files waiting to be moved to permanent storage
	•	These files are referenced by job records in the sf_jobs database table
	•	Automatically cleaned up by app/cron/cleanup_old_jobs.php

⸻

⚙️ Process-Based State System (CRITICAL)

The application uses a database-backed job queue (sf_jobs table) to manage background processing.

How it works:
	•	Each form submission inserts a row into sf_jobs:

INSERT INTO sf_jobs (flash_id, job_data, status) VALUES (?, ?, 'pending')


	•	The job_data JSON column contains:
	•	step data
	•	image references
	•	annotations
	•	layout configuration

	•	The background worker (process_flash_worker.php) reads the row, marks it in_progress, processes images, then marks it completed.

Used for:
	•	preview rendering
	•	step transitions
	•	temporary storage before final save

Automatic cleanup:
	•	app/cron/cleanup_old_jobs.php removes completed/failed rows older than 7 days and abandoned rows older than 30 days
	•	Recommended cron: 0 2 * * * php /path/to/cleanup_old_jobs.php

Database migration:
	•	Run database/migrations/001_create_sf_jobs.sql once to create the sf_jobs table

⸻

🧠 Form State Modes (CRITICAL)

The form operates in multiple modes:

Mode	Description
New	Create new SafetyFlash
Language Version	Clone existing
Investigation (with base)	Linked to existing
Investigation (no base)	Standalone

⚠️ This is a major source of bugs if not handled correctly.

⸻

⚠️ Known Issues & Sensitive Areas
	•	Preview loader may hang
	•	Image annotations may disappear in edit mode
	•	Card layout logic may desync (UI vs backend)
	•	Language version flow may show incorrect UI
	•	Form state logic is fragile and tightly coupled
	•	Email configuration partially hardcoded

⸻

🚧 Development Roadmap

High Priority
	•	Fix card layout logic consistency
	•	Stabilize preview rendering
	•	Fix image annotation persistence
	•	Refactor email system (remove hardcoding)

⸻

Medium Priority
	•	Add “Create Investigation” button in view
	•	Improve multilingual UX
	•	Improve mobile form usability

⸻

Low Priority
	•	UI polish
	•	Performance optimization
	•	Logging improvements

⸻

🧹 Maintenance Improvements (Recommended)
	•	Better separation of frontend/backend state
	•	Centralized configuration system

⸻

🔐 Security Considerations
	•	Restrict allowed email domains
	•	Prevent bot access:
	•	robots.txt
	•	authentication requirements
	•	Validate all input data
	•	Protect process files from public access

⸻

🏁 Summary

SafetyFlash is not just a publishing tool — it is a structured incident management communication system.

It ensures:
	•	Clear communication from incident to investigation
	•	Consistent and centralized publishing
	•	Traceable history of safety events
	•	Scalable and modern workflow
