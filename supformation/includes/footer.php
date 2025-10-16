<?php
// footer.php
?>
<footer style="background:#b30000; color:white; padding:20px; margin-top:40px; text-align:center;">
        <p>&copy; <?= date("Y") ?> Groupe Sup'Formation - Tous droits réservés.</p>

            <div class="footer-contacts">
                <a href="tel:+2252735999501" class="footer-phone">
                    <span class="phone-icon">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle; margin-right:7px;"><circle cx="11" cy="11" r="11" fill="#e03c3c"/><path d="M7.5 8.5C7.5 11.5376 10.4624 14.5 13.5 14.5L14.5 13.5C14.7761 13.2239 15.2239 13.2239 15.5 13.5L16.5 14.5C16.7761 14.7761 16.7761 15.2239 16.5 15.5C15.2239 16.7761 13.7761 18 12 18C8.13401 18 5 14.866 5 11C5 9.22386 6.22386 7.77614 7.5 6.5C7.77614 6.22386 8.22386 6.22386 8.5 6.5L9.5 7.5C9.77614 7.77614 9.77614 8.22386 9.5 8.5L8.5 9.5C8.22386 9.77614 7.77614 9.77614 7.5 9.5L7.5 8.5Z" fill="white"/></svg>
                    </span>
                    27 35 99 95 01
                </a>
                <a href="tel:+2250574939737" class="footer-phone">
                    <span class="phone-icon">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle; margin-right:7px;"><circle cx="11" cy="11" r="11" fill="#e03c3c"/><path d="M7.5 8.5C7.5 11.5376 10.4624 14.5 13.5 14.5L14.5 13.5C14.7761 13.2239 15.2239 13.2239 15.5 13.5L16.5 14.5C16.7761 14.7761 16.7761 15.2239 16.5 15.5C15.2239 16.7761 13.7761 18 12 18C8.13401 18 5 14.866 5 11C5 9.22386 6.22386 7.77614 7.5 6.5C7.77614 6.22386 8.22386 6.22386 8.5 6.5L9.5 7.5C9.77614 7.77614 9.77614 8.22386 9.5 8.5L8.5 9.5C8.22386 9.77614 7.77614 9.77614 7.5 9.5L7.5 8.5Z" fill="white"/></svg>
                    </span>
                    05 74 93 97 37
                </a>
                <a href="tel:+2250706591243" class="footer-phone">
                    <span class="phone-icon">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle; margin-right:7px;"><circle cx="11" cy="11" r="11" fill="#e03c3c"/><path d="M7.5 8.5C7.5 11.5376 10.4624 14.5 13.5 14.5L14.5 13.5C14.7761 13.2239 15.2239 13.2239 15.5 13.5L16.5 14.5C16.7761 14.7761 16.7761 15.2239 16.5 15.5C15.2239 16.7761 13.7761 18 12 18C8.13401 18 5 14.866 5 11C5 9.22386 6.22386 7.77614 7.5 6.5C7.77614 6.22386 8.22386 6.22386 8.5 6.5L9.5 7.5C9.77614 7.77614 9.77614 8.22386 9.5 8.5L8.5 9.5C8.22386 9.77614 7.77614 9.77614 7.5 9.5L7.5 8.5Z" fill="white"/></svg>
                    </span>
                    07 06 59 12 43
                </a>
            </div>

            <style>
                .footer-contacts {
                    margin-top:18px;
                    display:flex;
                    flex-wrap:wrap;
                    justify-content:center;
                    gap:18px;
                }
                .footer-phone {
                    background:#fff;
                    color:#b30000;
                    font-weight:700;
                    border-radius:22px;
                    padding:8px 18px;
                    text-decoration:none;
                    box-shadow:0 2px 12px rgba(179,0,0,0.08);
                    transition:background 0.2s, color 0.2s, transform 0.25s cubic-bezier(.4,0,.2,1);
                    font-size:1.08rem;
                    display: flex;
                    align-items: center;
                    position: relative;
                    animation: phonePop 1.1s cubic-bezier(.4,0,.2,1);
                }
                .footer-phone:hover, .footer-phone:focus {
                    background: #ffeaea;
                    color: #e03c3c;
                    transform: scale(1.08) translateY(-2px);
                    box-shadow: 0 6px 24px rgba(224,60,60,0.13);
                }
                @keyframes phonePop {
                    0% { opacity: 0; transform: scale(0.7) translateY(18px); }
                    60% { opacity: 1; transform: scale(1.08) translateY(-4px); }
                    100% { opacity: 1; transform: scale(1) translateY(0); }
                }
            </style>
</footer>
</body>
</html>
