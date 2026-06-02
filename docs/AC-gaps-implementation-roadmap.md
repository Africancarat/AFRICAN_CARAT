# AC-gaps implementation roadmap (vs monteluca.com)

This document tracks **phased scope**, **module sizing**, and a **codebase gap map**. It does not replace legal, tax, or operational sign-off—assign owners in §1.

---

## Current focus (active program)

Work is prioritized on the following. Everything else in the original AC-gaps.pdf is **out of scope for this phase** unless listed under [Deferred](#deferred-implement-later).

1. **International luxury jewelry sales** — Luxury catalog, unique/high-value SKU behavior, inventory integrity (e.g. soft reservation), sellable destinations, operational workflows for international orders.
2. **Referral commerce** — Partner accounts, referral codes, discount + commission rules (including tiered structure when ready), settlement in accounting currency; **no** AML/KYC on partners in this phase (see deferred).
3. **Multi-currency transactions** — Checkout currency, **locked FX** at cart/checkout with expiry, snapshot on order, base accounting currency (e.g. USD) for settlement reporting.
4. **International tax & customs** — Configurable VAT/GST/duty/luxury tax, **DDP vs DAP**, customer-facing logic and admin rules; CITES / restricted materials and customs documents as part of customs, not AML.
5. **Luxury logistics workflows** — High-value shipping paths, carrier integrations where contracted (Brinks, Malca-Amit, DHL HV, FedEx/UPS declared value, etc.), shipment insurance calculation, tracking hooks (OTP/signature only if carrier supports—**not** framed as fraud product).
6. **Hallmarking compliance** — UK / India (and extensible) hallmark data, admin verification workflow, region-based **sell restrictions** when data incomplete, audit timestamps and renewal/expiry reminders.
7. **Diamond certificate tracking** — GIA/IGI/HRD (and extensible), certificate linkage to SKU, PDF/QR/verification URL, **lifecycle for sale and shipment** (return/lost-certificate **refund** workflows deferred).
8. **Financial-grade ledger accounting** — Double-entry ledger for **payment captured**, **revenue**, **tax liabilities**, **commission expense/liability**, **shipping** as modeled; **no** refund, chargeback, or fraud-adjustment ledger posts in this phase (deferred).

---

## Deferred (implement later)

| Area | Notes |
|------|--------|
| **Cybersecurity (enterprise hardening)** | MFA, WAF/bot/geo policy, CSP program, API JWT/HMAC hardening beyond Laravel defaults—pick up after core commerce/ledger ships. |
| **AML / KYC** | Partner/customer KYC vault, threshold triggers, structured transaction monitoring, linked-account detection—explicitly later. |
| **Refunds** | Partial/full refund flows, proportional commission reversal, ledger reversal entries, payment-provider refund APIs—later phase. |
| **Fraud detection** | Return QC fraud flags, stone-swap heuristics, repeated return risk, investigation center—later phase. |

*Implication:* Referral partners can use a **lightweight onboarding** (business details, bank/payout, tax IDs as forms only) without verification gates. Orders are **sale-complete** only; post-sale disputes are manual/off-system until refund phase.

---

## 1. Ownership (business / legal)

| Area | Suggested owner | Role |
|------|-----------------|------|
| Product priority & acceptance | _TBD: Product owner_ | Scope for current focus, UAT |
| Tax / customs / DDP-DAP / CITES | _TBD: Legal / trade counsel_ | Country rules, restricted materials |
| Payments & ledger | _TBD: Tech lead / finance ops_ | Chart of accounts, reconciliation, FX source |
| Logistics & carriers | _TBD: Operations_ | Contracts, service levels |
| Hallmark / assay policy | _TBD: Compliance / ops_ | Which regions require which data |

_Deferred owners (when enabled):_ AML/KYC compliance officer; security/infra for hardening; fraud ops for return analytics.

---

## 2. Phased scope (aligned to current focus)

### Phase A — Foundation for sales + money accuracy

**Goal:** International luxury checkout with immutable pricing snapshots and no double-capture; ledger records the sale.

| Theme | Scope |
|-------|--------|
| International luxury sales | Soft inventory lock (TTL, session/user), oversell reduction; extend catalog/SKU for serial, vault location, certificate links as needed |
| Multi-currency | Persist checkout currency, locked rate, provider, timestamp on order lines/cart snapshot; align `PriceHelper` display with persisted snapshot at pay time |
| Ledger (financial-grade v1) | Double-entry posts for: payment received → cash clearing; revenue; tax payable; shipping revenue/expense as designed; commission accrual when referral applies |
| Payments plumbing | `DB::transaction()` for order persistence; `webhook_idempotency_logs` (or equivalent) for duplicate event safety |
| Referral (v1) | Partner + code + 5%/5% (or configured flat); attribute order; commission lines hit ledger |

**DB (subset):** `inventory_soft_locks`, `webhook_idempotency_logs`, `financial_ledger` (+ chart mapping table if needed), referral core tables, order FX/currency columns.

**Exit criteria:** One provider event → one paid order; FX/rates frozen on order; ledger balanced per event; referral commission visible in ledger export.

### Phase B — Tax, customs, hallmark, certificates, logistics

**Goal:** Sell only where compliant; move high-value goods with documented carriers and insurance.

| Theme | Scope |
|-------|--------|
| International tax & customs | Admin matrix: VAT/GST, duty, luxury tax; DDP vs DAP; landed cost messaging; CITES/restricted SKU flags + document storage + destination checks |
| Hallmarking | Certificates, assay metadata, region gates, admin workflow, audit log, renewal/expiry reminders |
| Diamond certificates | Issuer, number, PDF, QR/URL; mandatory for certified SKUs; **no** automated lost-cert fee/refund (deferred) |
| Luxury logistics | Carrier adapters as available; insured shipment records; order ↔ shipment linkage; tracking surfaced in admin (and customer where applicable) |
| Referral (v2) | Tiered commissions per volume bands; multi-currency settlement using same FX snapshot rules |

**Reporting (this phase):** Revenue, commission, VAT/GST summary, ledger trial balance, shipment status—not AML/fraud dashboards.

### Phase C — Depth & polish (still within focus)

| Theme | Scope |
|-------|--------|
| Catalog | Full diamond filter parity (4Cs + extras), bespoke/bridal collections, metal/purity facets per PDF §3 |
| Customs | Richer document types, permit expiry, operational alerts |
| Logistics | Additional carriers, insurance rules refinement |
| Ledger | Dimensional reporting (channel, region, currency), period close helpers |

---

## 3. Module sizing (indicative person-days)

Assumptions: experienced Laravel team, existing monteluca checkout; **engineering only**. Add **25–40%** for QA and integration friction. **Excluded:** AML, enterprise cyber, refund engine, fraud/return QC automation.

| Module | Person-days (range) | Depends on |
|--------|----------------------|------------|
| Soft inventory lock + oversell prevention | 12–28 | Cart/checkout, SKU model |
| Webhook idempotency + payment atomicity | 18–40 | Gateway webhooks, queues |
| Double-entry ledger (sales + commission, no refund) | 40–100 | CoA design, order/payment events |
| FX lock + order snapshot | 15–35 | FX API choice, schema |
| Referral v1 | 20–45 | Checkout attribution |
| Referral tiers + settlement reporting | 25–55 | Referral v1, ledger |
| Tax/customs DDP/DAP + rules | 40–100 | Legal matrix |
| CITES / customs documents | 30–80 | Tax/customs baseline |
| Hallmark workflow + region gate | 35–90 | Product × country rules |
| Diamond certificate tracking (sale-side) | 25–60 | Media storage, PDP/admin |
| High-value logistics + insurance | 50–150+ | Carrier contracts/APIs |
| Core reporting (revenue, tax, ledger, commission) | 30–70 | Ledger + orders |

**Rough totals (current focus only)**

- **Phase A:** ~105–210 person-days (often **~3–5 months** calendar, small team).
- **Phases A + B:** ~280–520 person-days.
- **A + B + C polish:** add ~40–120 person-days depending on carrier and catalog depth.

---

## 4. Codebase map: existing vs greenfield

Legend: **Present** / **Partial** / **Gap**

### 4.1 International luxury sales & payments

| Capability | Status | Notes |
|------------|--------|--------|
| Multi-gateway checkout | **Present** | `core/routes/web.php`; Stripe/Razorpay/PayPal patterns |
| Order after pay | **Present** | e.g. `StripeCheckout::stripeNotify` |
| Transactional pay → order | **Gap** | Unify + idempotency |
| Double-entry ledger | **Gap** | `Transaction` ≠ ledger |
| Soft inventory lock | **Gap** | |

### 4.2 Multi-currency

| Capability | Status | Notes |
|------------|--------|--------|
| Display conversion | **Partial** | `PriceHelper` + session `Currency` |
| Locked FX on order | **Gap** | |
| Settlement / reporting currency | **Gap** | |

### 4.3 Catalog & certificates

| Capability | Status | Notes |
|------------|--------|--------|
| Items, attributes, diamond data | **Partial** | `Item`, `DiamondAttribute`, certificate fields |
| Serial / vault / hallmark records | **Gap** | Model + admin |
| Full certificate lifecycle (returns) | **Deferred** | |

### 4.4 Referral

| Capability | Status | Notes |
|------------|--------|--------|
| Promo codes | **Present** | `PromoCode`, cart |
| Affiliate **product** rows | **Present** | Not partner referral engine |
| Partner referral + commission | **Gap** | |

### 4.5 Tax, customs, logistics

| Capability | Status | Notes |
|------------|--------|--------|
| Basic tax | **Partial** | `Tax`, states |
| DDP/DAP / duty / CITES | **Gap** | |
| Standard shipping | **Present** | `ShippingService`, `TrackOrder` |
| Luxury carriers + insurance | **Gap** | |

### 4.6 Hallmark & compliance (non-AML)

| Capability | Status | Notes |
|------------|--------|--------|
| Hallmark workflow + region gate | **Gap** | |
| AML / KYC | **Deferred** | |
| Cyber hardening program | **Deferred** | |
| Admin auth | **Present** | `auth:admin` |

### 4.7 Reporting

| Capability | Status | Notes |
|------------|--------|--------|
| Invoices / order views | **Present** | Back + user |
| Ledger + tax + commission reports | **Gap** | |

---

## 5. Suggested technical sequence

1. **Schema:** Order/cart FX snapshot fields; `inventory_soft_locks`; `webhook_idempotency_logs`; `financial_ledger` + accounts mapping; referral tables; later `customs_documents`, `hallmark_certificates`, `diamond_certificates` (normalized), `shipment_insurance`.
2. **Checkout:** Reservation on add-to-cart; release on TTL or successful pay; single code path persisting locked rates.
3. **Payments:** Idempotent handler + `DB::transaction()`; emit ledger entries **only for successful capture** (no refund posts yet).
4. **Referral:** Apply discount + record commission liability in same transaction as order finalization where possible.
5. **Tax/customs:** Rule engine by destination + incoterm; block checkout when illegal combination.
6. **Hallmark & certs:** Gate `Item` sellable regions; admin uploads and audit.
7. **Logistics:** Shipment entities linked to order; carrier modules; insurance calc from order value + lane.

---

## 6. Document control

| Version | Date | Notes |
|---------|------|--------|
| 1.0 | 2026-05-12 | Initial roadmap from AC-gaps.pdf vs monteluca |
| 1.1 | 2026-05-12 | Current focus: 8 themes; deferred cyber, AML, refund, fraud |
