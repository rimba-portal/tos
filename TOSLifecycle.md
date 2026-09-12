# Team Operating System Lifecycle Control Framework

Version: 1.0  
Status: Proposed Baseline  
Architecture: Rimba Team Operating System


## Purpose

TOS composes existing Rimba capabilities around `OrgTeam` without centralizing their domain ownership.

```text
OrgTeam
├── Offering Lifecycle -> rimba/tos
├── Workflow Lifecycle -> rimba/jalan + rimba/kerja
├── Supply Lifecycle   -> rimba/eam
├── Knowledge Lifecycle-> rimba/dms + rimba/lms
└── Resource Lifecycle -> rimba/wfm + orang + jawat + janji
```

## Operating Formula

```text
Resource + Supply + Knowledge + Workflow = Offering
```

## TOS-Owned Models

```text
Offering
OfferingRequest
TeamOffering
TeamWorkflow
TeamSupply
TeamKnowledge
TeamResource
```

## New EAM Models Required

```text
Supply
SupplyAssignment
SupplyEvent
```

## Shared Infrastructure

```text
OrgTeam     -> rimba/pihak
Version     -> rimba/versi
Attributes  -> rimba/sifat
AuditLog    -> rimba/jejak
Agreement   -> rimba/janji
```

## Boundary Decisions

- No generic `Resource` model. Staff remains the workforce entity.
- No generic `Knowledge` model. Document remains the controlled knowledge entity.
- No duplicate Workflow model. WorkflowBlueprint remains the workflow definition.
- No per-domain version models. Reuse polymorphic Version.
- Supply is non-human. Resource is human.
- TOS composes domains but does not take ownership of their internal execution.

## Controlled Documents

- `PACKAGE.md`
- `OfferingLifecycle.md`
- `WorkflowLifecycle.md`
- `SupplyLifecycle.md`
- `KnowledgeLifecycle.md`
- `ResourceLifecycle.md`

## End of Control Framework
