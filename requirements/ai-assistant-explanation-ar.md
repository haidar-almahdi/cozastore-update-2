# شرح تفصيلي: المساعد الذكي للتسوق (Smart Shopping Assistant / AI Assistant)

> هذا الملف مخصّص لفهم الميزة بعمق أمام اللجنة الفنية.  
> الشرح بالعربي مع الإبقاء على المصطلحات التقنية بين قوسين كما تظهر في الكود.

---

## 1) ما هي الميزة؟ (Overview)

المساعد الذكي ميزة داخل صفحة المنتجات (`/shop/products`) تسمح لأي زائر أو زبون أن يكتب طلبه بلغة طبيعية (Natural Language) مثل:

> «بدي فستان أسود تحت 60 دولار»

فيقوم النظام بـ:

1. فهم الطلب واستخراج فلاتر (Filters) منه (فئة، لون، سعر، مقاس، كلمات مفتاحية).
2. البحث داخل قاعدة بيانات المنتجات (Products Database) عن نتائج مناسبة.
3. إرجاع رد نصي + بطاقات منتجات + اقتراحات متابعة (Follow-ups).
4. *(اختياري)* تحسين صياغة الرد عبر مزوّد ذكاء اصطناعي خارجي (External AI API) مثل OpenAI-compatible chat completions.

**مهم جداً للجنة:**  
الميزة **تعمل حتى لو ما في مفتاح API خارجي**.  
الجزء الأساسي محلي (Local Rule-based + Database Search).  
الـ AI الخارجي طبقة إضافية لتحسين الرد فقط، وليست شرطاً لعمل الميزة.

---

## 2) أين تظهر في الواجهة؟ (UI Location)

الملف:

- `resources/views/shop/products/index.blade.php`

في أعلى صفحة المنتجات يوجد صندوق دردشة (Chat Box) بعنوان:

- **Smart Shopping Assistant**

مكوناته في الـ HTML:

| عنصر الواجهة | المعرّف (ID) | وظيفته |
|---|---|---|
| صندوق الرسائل | `#assistant-messages` | يعرض المحادثة |
| حقل الإدخال | `#assistant-input` | يكتب المستخدم طلبه |
| نموذج الإرسال | `#assistant-form` | يرسل الرسالة عبر AJAX |
| زر المسح | `#assistant-clear` | يمسح المحادثة من الشاشة ومن `sessionStorage` |

كود الـ JavaScript موجود في نفس الملف داخل قسم `@section('scripts')`.

---

## 3) مسار الطلب من البداية للنهاية (Request Lifecycle)

تخيّل السيناريو كسلسلة خطوات:

```
المستخدم يكتب رسالة
        ↓
JavaScript (fetch) يرسل POST JSON
        ↓
Route: POST /shop/api/assistant/chat
        ↓
Controller: AiAssistantController@chat
        ↓
Service: AiAssistantService->handle()
        ↓
1) extractFilters()
2) searchProducts()
3) (اختياري) askExternalAi()
4) buildReply() + buildFollowUps()
        ↓
JSON Response يرجع للواجهة
        ↓
الـ JS يعرض: reply + products + follow_ups
```

### 3.1 المسار (Route)

في الملف `routes/web.php`:

```php
Route::post('/api/assistant/chat', [AiAssistantController::class, 'chat'])
    ->name('assistant.chat');
```

داخل مجموعة:

- prefix: `shop`
- name prefix: `shop.`

إذن الاسم الكامل للمسار (Named Route):

- `shop.assistant.chat`
- الرابط الفعلي (URL): `/shop/api/assistant/chat`

**ملاحظة أمنية/وظيفية:**  
هذا المسار **عام** (لا يحتاج `auth`). أي زائر يقدر يستخدم المساعد بدون تسجيل دخول.

### 3.2 المتحكم (Controller)

الملف:

- `app/Http/Controllers/Shop/AiAssistantController.php`

الدالة:

- `chat(Request $request, AiAssistantService $assistant)`

ماذا تفعل بالضبط؟

1. تتحقق من صحة البيانات (Validation):
   - `message` مطلوب (required)
   - نص (string)
   - أقصى طول 1000 حرف (`max:1000`)
2. تستدعي الخدمة (Service):
   - `$assistant->handle($validated['message'])`
3. ترجع النتيجة كـ JSON:
   - `response()->json(...)`

**نقطة تقنية مهمة (Dependency Injection):**  
الـ Laravel يحقن `AiAssistantService` تلقائياً في الدالة.  
ما في حاجة لعمل `new AiAssistantService()` يدوياً.

### 3.3 الخدمة (Service) — العقل الحقيقي للميزة

الملف:

- `app/Services/AiAssistantService.php`

الدالة الرئيسية:

- `handle(string $message): array`

ترجع مصفوفة بهذا الشكل:

```json
{
  "reply": "نص الرد",
  "filters": { ... },
  "follow_ups": ["اعرض الأرخص", "..."],
  "products": [
    {
      "id": 1,
      "name": "...",
      "price": 49.99,
      "slug": "...",
      "image_url": "..."
    }
  ]
}
```

---

## 4) كيف يفهم المساعد كلام المستخدم؟ (extractFilters)

الدالة:

- `extractFilters(string $message): array`

هذه **ليست نموذج لغة كبير محلي**.  
هي محرك قواعد (Rule-based / Heuristic Parsing) يعمل هكذا:

### 4.1 تجهيز النص (Normalization)

- تحويل الرسالة لأحرف صغيرة (`mb_strtolower`)
- تنظيف الرموز عبر `normalizeText()`
- الإبقاء على الحروف والأرقام والمسافات فقط

### 4.2 استخراج الفئة (Category)

خريطة كلمات (Keyword Map) تربط كلمات المستخدم بـ `slug` الفئة:

| كلمة المستخدم | فئة النظام (slug) |
|---|---|
| فستان / dress / women | `womens-fashion` |
| رجالي / men | `mens-fashion` |
| حذاء / shoe / shoes | `shoes` |
| شنطة / bag | `bags` |
| اكسسوار / accessories | `accessories` |

إذا لقى الكلمة داخل النص → يضع `filters['category'] = slug`.

### 4.3 استخراج اللون (Color)

خريطة مشابهة:

- اسود / black → `black`
- ابيض / white → `white`
- احمر / red → `red`
- ازرق / blue → `blue`
- اخضر / green → `green`

### 4.4 استخراج السعر (Price Range)

باستخدام تعبير منتظم (Regular Expression / Regex):

- `تحت 60` أو `اقل من 60` أو `under 60` → `price_max = 60`
- `فوق 100` أو `اكتر من 100` أو `over 100` → `price_min = 100`

### 4.5 استخراج المقاس (Size)

Regex يلتقط:

- `مقاس M` أو `size xl` ... إلخ
- القيم المدعومة مثل: `XS, S, M, L, XL, XXL` أو رقم مقاس من خانتين

### 4.6 الكلمات المفتاحية (Keywords)

- يضيف الرسالة كاملة كعبارة واحدة
- ثم يقسمها إلى كلمات (Tokens) بطول ≥ 2
- يخزّنها في `filters['keywords']` بدون تكرار (`array_unique`)

**خلاصة للجنة:**  
الذكاء هنا = تحليل قواعدي للنص + بحث في قاعدة البيانات، وليس تدريباً لنموذج ML داخل السيرفر.

---

## 5) كيف يبحث عن المنتجات؟ (searchProducts)

الدالة:

- `searchProducts(array $filters)`

### 5.1 الشروط الأساسية

يبدأ الاستعلام (Eloquent Query):

```php
Product::query()->where('is_active', true)->with('category');
```

يعني: منتجات مفعّلة فقط، مع تحميل الفئة مسبقاً (Eager Loading) لتقليل استعلامات قاعدة البيانات.

### 5.2 تطبيق الفلاتر

| الفلتر | كيف يُطبَّق في SQL/Eloquent |
|---|---|
| category | `whereHas('category', slug = ...)` |
| price_min | `where('price', '>=', ...)` |
| price_max | `where('price', '<=', ...)` |
| size | `where('sizes', 'like', '%"M"%')` لأن المقاسات مخزّنة JSON |
| color | بحث في `colors` أو `name` أو `description` |
| keywords | بحث في `name` + `description` + عمود `ai_keywords` |

### 5.3 دور عمود `ai_keywords` (مهم جداً)

أضفنا عمود JSON في جدول المنتجات:

- Migration: `database/migrations/2026_03_31_000006_add_ai_keywords_to_products_table.php`
- العمود: `ai_keywords` من نوع `json` وقابل لأن يكون `nullable`

في الموديل `Product`:

- موجود في `$fillable`
- وcast إلى `array`

**لماذا أضفناه؟**  
لأن اسم المنتج ووصفه قد يكونان بالإنجليزية، بينما المستخدم يكتب بالعامية/العربية.  
مثال: منتج اسمه `Evening Gown`، والمدير يضيف كلمات:

`فستان سهرة, رسمي, طويل, اسود`

فيصير المساعد يقدر يربطه بطلب المستخدم.

البحث يستخدم:

- `orWhereJsonContains('ai_keywords', $kw)`

### 5.4 توسيع الكلمات (expandKeywordVariants)

قبل البحث، الكلمات تمر على `expandKeywordVariants()` لتعالج:

1. توحيد الحروف العربية (أ/إ/آ → ا ، ة → ه ، ى → ي ...)
2. قص آخر حرف للكلمات الطويلة (≥ 4) لتقليل أثر الأخطاء الإملائية البسيطة (مثل شنطة / شنتا)

### 5.5 طبقات الاحتياط (Fallbacks) — نقطة قوية للامتحان

النظام مصمم بعدة مستويات حتى ما يرجع فاضي بسرعة:

#### المستوى 1: بحث صارم (Strict Filters)
كل الفلاتر مع بعض.

#### المستوى 2: تخفيف الفلاتر
إذا ما طلع شيء، يعيد البحث بعد حذف:

- `category = null`
- `color = null`

ويحتفظ بالكلمات/السعر/المقاس.

#### المستوى 3: Fuzzy Match داخل PHP
إذا لسا فارغ، يجيب حتى 300 منتج عندهم `ai_keywords`، ويطابق الكلمات في PHP عبر `mb_strpos`.

في النهاية يأخذ حتى **8 منتجات** (`limit(8)` / `take(8)`).

---

## 6) كيف يبني الرد؟ (buildReply)

الدالة:

- `buildReply($message, $filters, $products): string`

الترتيب:

1. يحاول أولاً `askExternalAi(...)`
2. إذا رجعت نص → يستخدمه
3. إذا فشلت أو غير مفعّلة:
   - لا نتائج → رسالة: «ما لقيت نتائج مطابقة...»
   - في نتائج → رسالة محلية تلخّص الفلاتر المستخدمة + عدد المنتجات

يعني عندنا **Hybrid Approach**:

- Local logic دائماً متاح
- External AI اختياري لتحسين الصياغة والنصيحة

---

## 7) الـ AI الخارجي: منين جبناه وكيف فعلناه؟ (External AI Integration)

### 7.1 الفكرة العامة

الدالة:

- `askExternalAi(string $message, array $productNames): ?string`

تتواصل مع API خارجي متوافق مع صيغة OpenAI Chat Completions:

- endpoint يستقبل `model` + `messages`
- يرجع `choices[0].message.content`

### 7.2 من وين الإعدادات؟

ملف البيئة (Environment Variables) في `.env` (وموجود نموذجها في `.env.example`):

```env
AI_API_KEY=
AI_API_URL=
AI_MODEL=gpt-4o-mini
```

المعنى:

| المتغير | وظيفته |
|---|---|
| `AI_API_KEY` | مفتاح المصادقة (Bearer Token) |
| `AI_API_URL` | رابط الـ endpoint (مثلاً OpenAI أو مزوّد متوافق) |
| `AI_MODEL` | اسم النموذج، الافتراضي `gpt-4o-mini` |

### 7.3 كيف نفعّله فعلياً؟

1. نحصل على API Key من مزوّد يدعم Chat Completions (مثل OpenAI أو بديل متوافق).
2. نضع في `.env` مثلاً:

```env
AI_API_KEY=sk-xxxxxxxx
AI_API_URL=https://api.openai.com/v1/chat/completions
AI_MODEL=gpt-4o-mini
```

3. نمسح كاش الإعدادات إن لزم:

```bash
php artisan config:clear
```

4. بعدها `askExternalAi()` تصير ترجع نص بدل `null`.

### 7.4 ماذا يرسل الطلب؟

باستخدام Laravel HTTP Client:

```php
Http::timeout(12)
    ->withToken($key)
    ->post($url, [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => 'You are a shopping assistant for an ecommerce store.'],
            ['role' => 'user', 'content' => $prompt],
        ],
        'temperature' => 0.4,
    ]);
```

شرح الحقول:

- `timeout(12)`: أقصى انتظار 12 ثانية حتى ما يعلّق السيرفر
- `withToken($key)`: يرسل Authorization Bearer
- `system message`: يحدد شخصية المساعد
- `user prompt`: طلب المستخدم + أسماء المنتجات المطابقة
- `temperature = 0.4`: رد أكثر ثباتاً وأقل عشوائية

### 7.5 ماذا لو فشل الـ API؟

أي حالة من التالية ترجع `null` ويشتغل الرد المحلي:

- المفتاح أو الرابط فاضي
- الـ HTTP response مش OK
- Exception / Timeout / شبكة

هذا تصميم دفاعي (Defensive / Graceful Degradation):  
الميزة ما تنكسر إذا انقطع الـ AI الخارجي.

---

## 8) اقتراحات المتابعة (Follow-ups)

الدالة:

- `buildFollowUps(array $filters, int $count): array`

ترجع حتى 4 أزرار اقتراح مثل:

- «اعرض الأرخص»
- «أريد نفس المنتجات بألوان أخرى»
- «اعرض مقاس M»
- «اعرض فساتين»

في الواجهة، الضغط على الزر يملأ حقل الإدخال تلقائياً (`input.value = ...`) ويخلي المستخدم يرسل بسرعة.

---

## 9) واجهة الجافاسكربت بالتفصيل (Frontend JS)

الموقع: نهاية `shop/products/index.blade.php`

### 9.1 إرسال الرسالة

عند Submit للنموذج:

1. يمنع إعادة تحميل الصفحة (`e.preventDefault()`)
2. يعرض رسالة المستخدم فوراً
3. يرسل `fetch` إلى `route('shop.assistant.chat')` بطريقة POST JSON
4. يرسل CSRF Token في الهيدر:

```js
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
```

### 9.2 عرض النتيجة

بعد نجاح الطلب:

- `msg(data.reply)` → نص الرد
- `renderProducts(data.products)` → بطاقات منتجات قابلة للضغط (تربط لصفحة المنتج عبر `slug`)
- `renderActions(data.follow_ups)` → أزرار اقتراحات

### 9.3 حفظ المحادثة مؤقتاً

يستخدم:

- `sessionStorage` بمفتاح `assistant_chat_v1`

يعني:

- المحادثة تبقى إذا المستخدم حدّث الصفحة ضمن نفس تبويب المتصفح
- تنمسح إذا سكّر التبويب أو ضغط Clear

**ملاحظة:** هذا تخزين متصفح فقط، مش قاعدة بيانات.

---

## 10) كيف يضيف المدير كلمات المساعد؟ (Admin Side)

حتى يشتغل البحث العربي بشكل ممتاز، المدير يملأ حقل:

- **AI Keywords (Arabic)**

في صفحات:

- `resources/views/admin/products/create.blade.php`
- `resources/views/admin/products/edit.blade.php`

مثال إدخال:

```text
فستان سهرة, رسمي, طويل, اسود
```

في الـ Controller الإداري:

- `app/Http/Controllers/Admin/ProductController.php`
- الدالة الخاصة: `parseAiKeywords(?string $raw): array`

ماذا تفعل؟

1. تستبدل الفواصل العربية/الفاصلة المنقوطة/الأسطر بفواصل موحّدة
2. تقسم النص إلى كلمات
3. تحذف الفارغ والقصير جداً (< 2)
4. تحوّل لأحرف صغيرة
5. تزيل التكرار
6. تخزّن كمصفوفة JSON في `products.ai_keywords`

عند الحفظ في `store()` و `update()`:

```php
'ai_keywords' => $this->parseAiKeywords($request->input('ai_keywords')),
```

---

## 11) الملفات المرتبطة بالميزة (File Map)

احفظ هالقائمة؛ غالباً اللجنة تسأل «وين الكود؟»:

| الملف | الدور |
|---|---|
| `resources/views/shop/products/index.blade.php` | واجهة الشات + JavaScript |
| `routes/web.php` | تعريف المسار `shop.assistant.chat` |
| `app/Http/Controllers/Shop/AiAssistantController.php` | استقبال الطلب والتحقق منه |
| `app/Services/AiAssistantService.php` | المنطق الكامل (تحليل + بحث + رد) |
| `app/Models/Product.php` | الحقل `ai_keywords` + علاقات المنتج |
| `database/migrations/2026_03_31_000006_add_ai_keywords_to_products_table.php` | إضافة عمود JSON |
| `app/Http/Controllers/Admin/ProductController.php` | حفظ/تحديث الكلمات المفتاحية |
| `resources/views/admin/products/create.blade.php` | نموذج إدخال الكلمات |
| `resources/views/admin/products/edit.blade.php` | تعديل الكلمات |
| `.env` / `.env.example` | تفعيل الـ AI الخارجي اختيارياً |

---

## 12) معمارية التصميم باختصار (Architecture)

استخدمنا فصل المسؤوليات (Separation of Concerns):

1. **View/JS**: عرض وتفاعل المستخدم
2. **Controller**: بوابة رقيقة (Thin Controller) — validation فقط
3. **Service**: منطق الأعمال (Business Logic)
4. **Model/DB**: تخزين المنتجات والكلمات
5. **External API**: طبقة اختيارية لتحسين الرد

هذا أفضل من حشر كل المنطق داخل الـ Controller، ويسهّل الاختبار والصيانة.

---

## 13) مثال تدفّق كامل (End-to-End Example)

طلب المستخدم:

> بدي فستان اسود تحت 60

### ماذا يستخرج `extractFilters`؟

- `category = womens-fashion` (بسبب «فستان»)
- `color = black` (بسبب «اسود»)
- `price_max = 60` (بسبب «تحت 60»)
- `keywords` فيها كلمات الرسالة

### ماذا يفعل البحث؟

منتجات `is_active = true` في فئة النساء، لون أسود، سعر ≤ 60، مع مطابقة كلمات/`ai_keywords`.

### ماذا يرجع JSON تقريباً؟

```json
{
  "reply": "لقيت 3 منتجات مناسبة بناءً على الفئة + حد السعر + اللون.",
  "filters": {
    "category": "womens-fashion",
    "price_max": 60,
    "color": "black",
    "size": null,
    "keywords": ["بدي فستان اسود تحت 60", "بدي", "فستان", "..."]
  },
  "follow_ups": ["اعرض الأرخص", "أريد نفس المنتجات بألوان أخرى", "..."],
  "products": [ { "name": "...", "price": 49.99, "slug": "..." } ]
}
```

وإذا `AI_API_KEY` و `AI_API_URL` مفعّلين، ممكن يصير `reply` نصاً أغنى من الـ API الخارجي، لكن قائمة المنتجات تبقى من قاعدة بياناتنا نحن.

---

## 14) أسئلة متوقعة من اللجنة + إجابات مختصرة

### س1: هل المساعد يعتمد بالكامل على ChatGPT؟
**لا.** البحث والتوصية محليان من قاعدة البيانات. الـ API الخارجي اختياري لصياغة الرد فقط.

### س2: لماذا أضفتم `ai_keywords`؟
ل bridging بين لغة المستخدم (عربي/عامية) وبيانات المنتج (غالباً إنجليزي/تقنية)، وتحسين دقة المطابقة.

### س3: وين الـ Business Logic؟
في `AiAssistantService` وليس في الـ Controller.

### س4: هل يحتاج تسجيل دخول؟
لا. المسار خارج مجموعة `auth`.

### س5: ماذا لو انقطع الإنترنت عن مزوّد الـ AI؟
الميزة تستمر بالرد المحلي والبحث المحلي (Graceful Degradation).

### س6: كيف تحمون من طلبات عبثية طويلة؟
Validation: `message` بحد أقصى 1000 حرف + timeout للـ HTTP الخارجي 12 ثانية.

### س7: هل تحفظون تاريخ المحادثات في قاعدة البيانات؟
حالياً لا. التخزين في `sessionStorage` على المتصفح فقط.

### س8: كيف تربطون المنتج الظاهر في الشات بصفحته؟
عبر `slug` في الرابط: `/shop/products/{slug}`.

---

## 15) حدود الميزة الحالية (Limitations) — مهم تكون صريح فيها

1. فهم اللغة قواعدي، مو فهم سياقي عميق مثل LLM كامل محلي.
2. خرائط الفئات/الألوان محدودة بكلمات معرّفة مسبقاً.
3. ما في ذاكرة محادثة من طرف السيرفر بين الرسائل (كل طلب مستقل تقريباً على الـ backend).
4. جودة النتائج تعتمد كثيراً على جودة `ai_keywords` التي يدخلها المدير.
5. الـ AI الخارجي يحتاج مفتاح وتكلفة استخدام حسب المزوّد.

---

## 16) جملة ختامية تقدر تقولها للجنة

> «المساعد الذكي عندنا Hybrid Shopping Assistant: الواجهة Chat UI في صفحة المنتجات، والطلب يمر عبر API داخلي إلى Service تحلل النص قواعدياً وتبحث في المنتجات مع دعم عمود `ai_keywords`، مع طبقة اختيارية لمزوّد AI خارجي لتحسين صياغة الرد بدون ما تعتمد عليه الميزة بالكامل.»

---

## 17) أوامر تشغيل مفيدة أثناء العرض

إذا عدّلت الـ Migration وما اشتغلت بعد:

```bash
php artisan migrate
```

إذا غيّرت `.env` للـ AI:

```bash
php artisan config:clear
```

ثم افتح:

- `http://localhost/cozastore/cozastore/public/shop/products`  
  *(عدّل المسار حسب إعداد XAMPP عندك)*

وجرب رسالة مثل:

- `بدي فستان اسود تحت 60`
- `حذاء رجالي مقاس 42`
- `شنطة اقل من 100`

---

**آخر تنبيه للمذاكرة:**  
افهم الفرق بين الثلاث طبقات جيداً:

1. **UI/JS** = عرض وإرسال
2. **Local Intelligence** = `extractFilters` + `searchProducts`
3. **External AI** = `askExternalAi` (اختياري)

إذا ضبطت هالثلاث، بتقدر تجاوب تقريباً على أي سؤال فني عن المساعد الذكي في المشروع.
)
