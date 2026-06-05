# Shopbud

Shopbud helps a user model a recurring grocery shopping route so their shopping list follows the physical order of a supermarket.

## Language

**Template**:
A reusable supermarket structure owned by a user, containing a user-unique supermarket name and the ordered sectors the user normally walks through.
_Avoid_: List, shopping list, store header

**Sector**:
A template-unique supermarket aisle or area inside a Template, ordered according to the user's usual route through that supermarket.
_Avoid_: Category, section, corridor

**Product**:
An unordered sector-unique item the user expects to find inside a Sector of a Template.
_Avoid_: Shopping item, purchase item

**Shopping Item**:
A session-specific item inside an existing Shopping Session sector, derived from a Product or added during the shopping run, carrying purchase details for that run.
_Avoid_: Product

**Shopping Session**:
A concrete shopping run created from the current state of a Template, independent from later changes to that Template.
_Avoid_: Template instance, active list

**Snapshot**:
The immutable copy of a Template's sectors and products captured when a Shopping Session or shared template is created.
_Avoid_: Live template data, reference

**Shared Template**:
A temporary shared access to a Template's snapshot, valid only while the original Template remains available.
_Avoid_: Shopping Session, public template
