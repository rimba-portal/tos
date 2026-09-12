# Rimba TOS Package Architecture

Version: 1.0  
Status: Proposed Baseline  
Architecture: Rimba Team Operating System


## Purpose

`rimba/tos` is the integration and governance package for a Team Operating System. It does not duplicate domain models already owned by existing Rimba packages.

TOS governs five connected lifecycles:

```text
Resource + Supply + Knowledge + Workflow = Offering
```

## Package Boundary

`rimba/tos` owns only the Team-facing catalog and the explicit relationships between an `OrgTeam` and the five lifecycle domains.

It shall not own:

- workforce records
- workflow execution
- task execution
- documents or learning content
- asset internals
- organization structure
- generic versions or attributes

Those remain with their existing packages.

## Existing Package Ownership

```text
rimba/pihak   -> organization and OrgTeam
rimba/tos     -> Offering, team catalog, TOS relationships
rimba/jalan   -> workflow definition and execution
rimba/kerja   -> WorkPackage, Checklist, Task and instances
rimba/dms     -> controlled documents
rimba/lms     -> learning and competency evidence
rimba/wfm     -> workforce planning, assignments and events
rimba/orang   -> Staff
rimba/jawat   -> JobPosition and JobRole
rimba/janji   -> Agreement and Party
rimba/eam     -> Supply lifecycle implementation
rimba/versi   -> reusable polymorphic Version
rimba/sifat   -> reusable attributes
rimba/jejak   -> audit trail
```

## Final Minimal TOS Models

### Models owned by `rimba/tos`

```text
Offering
OfferingRequest
TeamOffering
TeamWorkflow
TeamSupply
TeamKnowledge
TeamResource
```

### Why the relationship models remain

The five relationship models are not duplicates of the domain entities. They record a Team's lifecycle-specific relationship to an entity, including ownership, responsibility, status and effective period.

They prevent direct `org_team_id` columns from becoming the only source of truth when an entity is shared, transferred, supported or consumed by multiple Teams.

## Model Structure

### Offering

Represents value made available by a Team.

```text
Offering
- id
- uuid
- code
- name
- type
- description
- status
- owner_team_id
- workflow_blueprint_id nullable
- active_version_id nullable
- effective_from nullable
- effective_until nullable
- attributes json nullable
- timestamps
```

Version history uses `Rimba\\Versioning\\Models\\Version` through `HasVersions`.

### OfferingRequest

Represents demand for an Offering and becomes the trackable business record for workflow execution.

```text
OfferingRequest
- id
- uuid
- offering_id
- requester_id nullable
- requested_for_type nullable
- requested_for_id nullable
- status
- priority nullable
- requested_at
- required_at nullable
- fulfilled_at nullable
- workflow_instance_id nullable
- details json nullable
- attributes json nullable
- timestamps
```

### TeamOffering

```text
TeamOffering
- id
- org_team_id
- offering_id
- relationship_type
- status
- effective_from
- effective_until nullable
- is_primary
- attributes json nullable
- timestamps
```

Suggested relationship types: `owner`, `provider`, `supporter`.

### TeamWorkflow

```text
TeamWorkflow
- id
- org_team_id
- workflow_blueprint_id
- relationship_type
- status
- effective_from
- effective_until nullable
- is_primary
- attributes json nullable
- timestamps
```

Suggested relationship types: `owner`, `operator`, `approver`, `supporter`.

### TeamSupply

```text
TeamSupply
- id
- org_team_id
- supply_id
- relationship_type
- status
- effective_from
- effective_until nullable
- quantity nullable
- attributes json nullable
- timestamps
```

Suggested relationship types: `owner`, `custodian`, `consumer`, `maintainer`.

### TeamKnowledge

The MVP links Teams to the existing controlled `Document` model. Courses and Modules remain connected through DMS/LMS relationships rather than being duplicated here.

```text
TeamKnowledge
- id
- org_team_id
- document_id
- relationship_type
- status
- effective_from
- effective_until nullable
- is_mandatory
- attributes json nullable
- timestamps
```

Suggested relationship types: `owner`, `author`, `reviewer`, `audience`.

### TeamResource

The MVP links Teams to `WorkforceAssignment`, not directly to `Staff`. The assignment already preserves the Staff, organization, position, manager, agreement, shift and effective period.

```text
TeamResource
- id
- org_team_id
- workforce_assignment_id
- relationship_type
- status
- effective_from
- effective_until nullable
- allocation_percentage nullable
- attributes json nullable
- timestamps
```

Suggested relationship types: `member`, `manager`, `contributor`, `supporter`.

## Models Reused From Existing Packages

### Workflow

```text
WorkflowBlueprint
WorkflowNode
WorkflowTransition
WorkflowInstance
WorkflowNodeInstance
WorkflowTransitionInstance
WorkPackage
Checklist
Task
WorkPackageInstance
ChecklistInstance
TaskInstance
```

### Knowledge

```text
Document
DocumentCategory
DocumentApproval
DocumentReview
DocumentDistribution
DocumentAcknowledgement
DocumentRetention
Course
Module
ModuleDocument
CourseEnrollment
Evaluation
Certificate
```

### Resource

```text
Staff
JobPosition
JobRole
Agreement
AgreementType
Party
WorkforcePlan
WorkforceRequirement
ManpowerRequest
Candidate
JobApplication
CandidateShortlist
WorkforceAssignment
WorkforceEvent
```

### Supply

The current `rimba/eam` package has a service provider but no domain models in the supplied class blueprint. The MVP implementation should add:

```text
Supply
SupplyAssignment
SupplyEvent
```

Recommended ownership: `Rimba\\Eam\\Models`.

## Minimal Supply Models in `rimba/eam`

### Supply

```text
Supply
- id
- uuid
- parent_id nullable
- code
- name
- type
- status
- org_corp_id nullable
- location_id nullable
- custodian_staff_id nullable
- serial_number nullable
- quantity nullable
- unit nullable
- acquired_at nullable
- available_from nullable
- retired_at nullable
- attributes json nullable
- timestamps
```

### SupplyAssignment

```text
SupplyAssignment
- id
- uuid
- supply_id
- assignable_type
- assignable_id
- assigned_by_id nullable
- quantity nullable
- status
- effective_from
- effective_until nullable
- returned_at nullable
- attributes json nullable
- timestamps
```

`assignable` may point to an `OrgTeam`, `Staff`, `WorkforceAssignment`, `WorkflowBlueprint`, `Offering`, or `Location`.

### SupplyEvent

```text
SupplyEvent
- id
- uuid
- supply_id
- supply_assignment_id nullable
- event_type
- effective_at
- source nullable
- source_reference nullable
- triggerable_type nullable
- triggerable_id nullable
- before_state json nullable
- after_state json nullable
- remarks nullable
- recorded_by_id nullable
- attributes json nullable
- timestamps
```

## Important Modifications to Existing Models

### `OrgTeam`

Add relationships only. No new lifecycle columns are required.

```text
offerings()
workflowRelations()
supplyRelations()
knowledgeRelations()
resourceRelations()
```

### `Offering`

Use `HasVersions` from `rimba/versi`. Do not create `OfferingVersion`.

### `Document`

Keep `team_id` as document ownership if already used. `TeamKnowledge` adds broader lifecycle relationships such as audience, reviewer, or shared ownership.

### `WorkforceAssignment`

Keep `org_team_id` as the authoritative workforce assignment. `TeamResource` is used only when TOS requires additional allocation or cross-team participation.

### `WorkflowBlueprint`

Keep workflow execution ownership in `rimba/jalan`. Use `TeamWorkflow` for Team governance relationships.

## Dependency Direction

```text
pihak <- tos -> jalan
            -> dms
            -> wfm
            -> eam
            -> versi
            -> sifat
```

`jalan`, `dms`, `wfm`, and `eam` must not depend on `tos` merely to function. TOS composes them at the Team level.

## MVP Implementation Order

1. Implement `Supply`, `SupplyAssignment`, and `SupplyEvent` in `rimba/eam`.
2. Implement `Offering` and `OfferingRequest` in `rimba/tos`.
3. Implement the five Team relationship models.
4. Add model relationships and indexes.
5. Add JSON seed definitions.
6. Add Filament resources only for Offering, OfferingRequest, and Supply initially.
7. Use existing UI/resources for Workflow, Knowledge and Resource domains.

## End of Architecture
