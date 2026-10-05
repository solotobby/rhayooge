<div style="min-height:85vh;display:flex;align-items:center;justify-content:center;padding:2.5rem 1.25rem;">
    <div style="max-width:480px;width:100%;background:#ffffff;border:1px solid #ebe5dc;border-radius:14px;padding:2.75rem 2.25rem;box-shadow:0 10px 30px rgba(0,0,0,0.03);">
        <!-- Atelier Brand Header -->
        <div style="text-align:center;margin-bottom:2rem;">
            <a href="{{ route('home') }}" style="text-decoration:none;">
                <span style="font-family:var(--font-serif);font-size:1.6rem;font-weight:600;letter-spacing:0.12em;color:#221f1e;text-transform:uppercase;">
                    RHÁYỌ̀OGE
                </span>
            </a>
            <div style="margin-top:0.4rem;">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4ede4] px-3 py-0.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#715b4d]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#2e7d32]"></span> Partner Desk
                </span>
            </div>
        </div>

        @if (session('executive_login_notice'))
            <div style="background:#f4fbf5;border:1px solid #ccebd1;color:#1e5e2b;padding:0.85rem 1rem;border-radius:8px;font-size:0.85rem;margin-bottom:1.5rem;line-height:1.4;text-align:center;">
                {{ session('executive_login_notice') }}
            </div>
        @endif

        @if (session('executive_login_error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.85rem 1rem;border-radius:8px;font-size:0.85rem;margin-bottom:1.5rem;line-height:1.4;text-align:center;">
                {{ session('executive_login_error') }}
            </div>
        @endif

        @if ($currentExecutive && ! $sent)
            <div style="background:#faf8f5;border:1px solid #ebd8cb;border-radius:10px;padding:1.5rem;text-align:center;margin-bottom:2rem;">
                <p class="eyebrow" style="margin-bottom:0.25rem;color:var(--terracotta,#b85d38);">Active Partner Session</p>
                <h2 style="font-family:var(--font-serif);font-size:1.5rem;margin:0 0 0.35rem;color:#221f1e;font-weight:500;">
                    {{ $currentExecutive->name }}
                </h2>
                <p style="margin:0 0 1.25rem;font-size:0.85rem;color:#78695d;">
                    Referral Code: <code style="font-weight:600;">@{{ $currentExecutive->code }}</code> &bull; {{ $currentExecutive->default_commission_rate }}% Commission
                </p>
                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    <a href="{{ route('executive.dashboard') }}" class="btn btn-primary btn-full" style="padding:0.8rem 1rem;font-size:0.85rem;text-decoration:none;display:inline-block;border-radius:6px;box-sizing:border-box;">
                        Enter Partner Dashboard &rarr;
                    </a>
                    <button type="button" wire:click="logoutCurrent" class="btn btn-ghost" style="font-size:0.78rem;padding:0.45rem 0.8rem;border:1px solid #dfd8cc;border-radius:6px;cursor:pointer;color:#78695d;">
                        Sign Out / Switch Account
                    </button>
                </div>
            </div>
        @endif

        @if (! $sent && ! $currentExecutive)
            <div style="margin-bottom:1.75rem;text-align:center;">
                <h1 style="font-family:var(--font-serif);font-size:1.85rem;margin:0 0 0.4rem;font-weight:500;color:#221f1e;">
                    Partner Sign In
                </h1>
                <p style="margin:0;font-size:0.9rem;color:#78695d;line-height:1.5;">
                    Enter your registered email address, referral code, or phone number to receive an authenticated magic login link.
                </p>
            </div>

            @if ($errorMessage)
                <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.85rem 1rem;border-radius:8px;font-size:0.85rem;margin-bottom:1.5rem;line-height:1.4;">
                    {{ $errorMessage }}
                </div>
            @endif

            <form wire:submit="sendMagicLink" style="display:flex;flex-direction:column;gap:1.25rem;">
                <div>
                    <label class="admin-label" style="display:block;margin-bottom:0.4rem;">
                        Email, Referral Code, or Phone
                    </label>
                    <input
                        type="text"
                        wire:model="identifier"
                        placeholder="e.g. partner@example.com or @oluwatobis"
                        class="admin-input"
                        autofocus
                        required
                        style="padding:0.75rem 1rem;font-size:0.92rem;border-radius:8px;"
                    >
                    @error('identifier')
                        <p class="form-error" style="font-size:0.78rem;color:#b91c1c;margin-top:0.35rem;">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-full"
                    style="padding:0.85rem 1.5rem;font-size:0.88rem;letter-spacing:0.08em;border-radius:8px;cursor:pointer;"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Send Magic Link</span>
                    <span wire:loading>Generating Link...</span>
                </button>
            </form>

            <div style="margin-top:2rem;text-align:center;border-top:1px solid #f0eee9;padding-top:1.25rem;">
                <p style="margin:0;font-size:0.8rem;color:#a39284;">
                    Confidential partner portal for authorized executive affiliates.
                </p>
                <div style="margin-top:0.6rem;">
                    <a href="{{ route('home') }}" style="font-size:0.78rem;color:#8c7362;text-decoration:none;">
                        &larr; Return to Storefront
                    </a>
                </div>
            </div>
        @else
            <!-- Success Screen -->
            <div style="text-align:center;">
                <div style="width:52px;height:52px;background:#e8f4e9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;color:#2e7d32;font-size:1.5rem;">
                    &#10003;
                </div>

                <h2 style="font-family:var(--font-serif);font-size:1.75rem;margin:0 0 0.5rem;font-weight:500;color:#221f1e;">
                    Magic Link Sent!
                </h2>

                <p style="margin:0 0 1.25rem;font-size:0.92rem;color:#78695d;line-height:1.5;">
                    Hello <strong>{{ $sentName }}</strong>, we have dispatched your private magic access link to:
                </p>

                <div style="background:#faf8f5;border:1px solid #eee7dc;padding:0.85rem 1rem;border-radius:8px;margin-bottom:1.5rem;">
                    <strong style="color:#221f1e;font-size:0.95rem;">{{ $sentEmail }}</strong>
                </div>

                <p style="margin:0 0 1.75rem;font-size:0.82rem;color:#8c7362;line-height:1.5;">
                    Open the email on your device and click <strong>"Sign In to Partner Dashboard"</strong>. The link is authenticated and valid for 72 hours.
                </p>

                <!-- Direct Access Button (Works in Local / Dev Mode) -->
                @if ($directMagicUrl)
                    <div style="margin-bottom:1.5rem;background:#fdfaf6;border:1px dashed #d8c3b4;padding:1.2rem;border-radius:8px;text-align:center;">
                        <span style="display:block;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:#b85d38;font-weight:600;margin-bottom:0.4rem;">
                            Direct Workspace Access
                        </span>
                        <a
                            href="{{ $directMagicUrl }}"
                            class="btn btn-primary btn-full"
                            style="padding:0.75rem 1.25rem;font-size:0.82rem;text-decoration:none;display:inline-block;box-sizing:border-box;border-radius:6px;"
                        >
                            Open Dashboard Now &rarr;
                        </a>
                    </div>
                @endif

                <div>
                    <button
                        type="button"
                        wire:click="resetForm"
                        style="background:none;border:none;color:#8c7362;font-size:0.82rem;cursor:pointer;text-decoration:underline;"
                    >
                        Sign in with a different account
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
